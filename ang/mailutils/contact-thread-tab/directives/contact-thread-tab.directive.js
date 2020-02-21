(function (angular, $, _) {
  var module = angular.module('mailutils');

  module.directive('mailutilsContactThreadTab', function () {
    return {
      restrict: 'EA',
      controller: 'MailutilsContactThreadTabController',
      templateUrl: '~/mailutils/contact-thread-tab/directives/contact-thread-tab.directive.html',
      scope: {}
    };
  });

  module.controller('MailutilsContactThreadTabController', MailutilsContactThreadTabController);

  /**
   * @param {object} $scope the controller scope
   * @param {Function} crmApi4 the crm api service
   * @param {object} Contact the contact service
   */
  function MailutilsContactThreadTabController($scope, crmApi4, Contact, DateHelper) {
    var commonConfigs = {
      isLoaded: false,
      showSpinner: false,
      isLoadMoreAvailable: false,
      page: {
        size: 3,
        num: 1
      }
    };
    $scope.viewingThread = false;
    $scope.selectedThreads = [];
    $scope.selectedThread = null;
    $scope.bulkAllowed = true;
    $scope.isSelectAllAvailable = true;
    $scope.contactId = Contact.getContactIDFromUrl();
    $scope.checkPerm = CRM.checkPerm;
    $scope.ts = CRM.ts('mailutils');
    $scope.threads = [];
    $scope.threadParties = [[{email: 'foo@example.com'}]];
    $scope.sort = { sortable: true };
    $scope.headers = [
      {
        name: 'from_to',
        label: 'From/To',
        sort: null,
      },
      {
        name: 'subject',
        label: 'Subject',
        sort: null,
      },
      {
        name: 'date',
        label: 'Date',
        sort: 'ASC',
      }
    ];

    $scope.formatDate = DateHelper.formatDate;
    $scope.calendar = DateHelper.calendar;

    /**
     * Update Bulk Actions checkbox of the case card
     */
    $scope.toggleSelected = function (thread) {
      thread.selected = !thread.selected;
      $scope.$emit('mailutils::bulk-actions::check-box-toggled', $scope.data);
    };

    $scope.viewThread = function (thread) {
      $scope.selectedThread = thread;
      $scope.viewingThread = true;
    };

    $scope.showThreads = function(refresh) {
      $scope.viewingThread = false;
      $scope.selectedThread = null;
      if (refresh) {
        getThreads();
      }
    };

    $scope.deleteThread = function(threadId) {
      crmApi4('MailutilsThread', 'delete', {
        where: [['id', '=', threadId]]
      }).then(function(results) {
        $scope.showThreads(true);
      });
    };

    (function init () {
      initMailutilsConfig();
      initSubscribers();
      getThreads();
    }());

    /**
     * refresh function to set refresh cases
     */
    $scope.refresh = function () {
      initMailutilsConfig();
      getThreads();
    };

    /**
     * Watcher for civicase::contact-record-list::loadmore event
     *
     * @param {object} event scope watch event reference
     * @param {string} name of the list
     */
    function contactRecordListLoadmoreWatcher (event, name) {
      var caseListIndex = _.findIndex($scope.casesListConfig, function (caseObj) {
        return caseObj.name === name;
      });
      var params = getCaseApiParams($scope.casesListConfig[caseListIndex].filterParams, $scope.casesListConfig[caseListIndex].page);

      $scope.casesListConfig[caseListIndex].showSpinner = true;
      updateCase(caseListIndex, params);
    }

    /**
     * Watcher for civicase::contact-record-list::view-case event
     *
     * @param {object} event scope watch event reference
     * @param {object} caseObj the data belonging to a case
     */
    function contactRecordListViewCaseWatcher (event, caseObj) {
      setCaseAsSelected(caseObj);
    }

    /**
     * Fetch additional information about the contacts
     *
     * @param {object[]} cases a list of cases
     */
    function fetchContactsData (cases) {
      var contacts = [];

      _.each(cases, function (caseObj) {
        contacts = contacts.concat(getAllContactIdsForCase(caseObj));
      });

      ContactsCache.add(contacts);
    }

    /**
     * Returns all the contact ids for the given case
     *
     * @param {object} caseObj the data belonging to a case
     * @returns {number[]} a list of contact ids
     */
    function getAllContactIdsForCase (caseObj) {
      var contacts = [];

      _.each(caseObj.contacts, function (currentCase) {
        contacts.push(currentCase.contact_id);
      });

      _.each(caseObj.activity_summary.next, function (activity) {
        contacts = contacts.concat(activity.assignee_contact_id);
        contacts = contacts.concat(activity.target_contact_id);
        contacts.push(activity.source_contact_id);
      });

      return contacts;
    }

    function getThreads () {
      $scope.threads = [];
      // get total thread count for contact
      crmApi4('MailutilsThread', 'get', {
        select: ['row_count'],
        where: [['involved_contact_id', '=', $scope.contactId]]
      }).then(function(mailutilsThreads) {
        $scope.totalCount = mailutilsThreads.count;
      });
      crmApi4('MailutilsThread', 'get', {
        select: [
          'id',
          'mailutils_messages.subject',
          'mailutils_messages.subject_normalized',
          'mailutils_messages.activity_id'
        ],
        where: [['involved_contact_id', '=', $scope.contactId]],
        limit: 25
      }).then(function(threads) {
        threads.forEach(function(thread) {
          thread.mailutils_message_parties = [];
          thread.activities = [];
          var threadIndex = $scope.threads.push(thread) - 1;
          thread.mailutils_messages.forEach(function(message) {
            CRM.api4('MailutilsMessageParty', 'get', {
              select: ['name', 'email', 'contact_id'],
              where: [['mailutils_message_id', '=', message.id]]
            }).then(function (parties) {
              parties.forEach(function(party) {
                $scope.threads[threadIndex].mailutils_message_parties.push(party);
              });
              // uniqueify based on party name
              $scope.threads[threadIndex].mailutils_message_party_names = $scope.threads[threadIndex].mailutils_message_parties.map(function(party) {
                return party.name;
              }).filter(function (value, index, self) {
                return self.indexOf(value) === index;
              });
              $scope.$apply();
            });
          });
          CRM.api4('Activity', 'get', {
            select: ['activity_date_time'],
            where: [['id', 'IN', thread.mailutils_messages.map(function(message) {
              return message.activity_id;
            })]],
            orderBy: {activity_date_time: 'DESC'},
            limit: 1
          }, 0).then(function(activity) {
            $scope.threads[threadIndex].date = activity.activity_date_time
            $scope.$apply();
          });

          CRM.api4('Activity', 'get', {
            where: [['id', 'IN', thread.mailutils_messages.map(function(message) {
              return message.activity_id;
            })]],
            orderBy: {activity_date_time: 'DESC'}
          }).then(function(activities) {
            $scope.threads[threadIndex].activities = activities;
            $scope.$apply();
          });

        });
      });
      /*function updateCase (caseListIndex, params) {
        crmApi(params).then(function (response) {
          _.each(response.cases.values, function (item) {
            item.contact_role = getContactRole(item);
            $scope.casesListConfig[caseListIndex].cases.push(formatCase(item));
          });

          $scope.casesListConfig[caseListIndex].isLoaded = true;
          $scope.casesListConfig[caseListIndex].showSpinner = false;
          $scope.casesListConfig[caseListIndex].isLoadMoreAvailable = $scope.casesListConfig[caseListIndex].cases.length < response.count;

          if ($scope.casesListConfig[caseListIndex].page.num === 1) {
            loadAdditionalDataWhenAllCasesLoaded();
          }

          $scope.casesListConfig[caseListIndex].page.num += 1;
        });
      }
      var totalCountApi = [];

      _.each($scope.casesListConfig, function (item, ind) {
        var params = getCaseApiParams(item.filterParams, item.page);

        updateCase(ind, params);
        totalCountApi.push(params.count);
      });

      getTotalCasesCount(totalCountApi);*/
    }

    /**
     * Get parameters to load cases
     *
     * @param {object} filter the filters to use when loading the cases
     * @param {object} page the current page and the page size
     * @returns {object} the parameters needed to load cases
     */
    function getCaseApiParams (filter, page) {
      /*
      var caseReturnParams = [
        'subject', 'details', 'contact_id', 'case_type_id', 'status_id',
        'contacts', 'start_date', 'end_date', 'is_deleted', 'activity_summary',
        'activity_count', 'category_count', 'tag_id.name', 'tag_id.color',
        'tag_id.description', 'tag_id.parent_id', 'related_case_ids'
      ];
      var returnCaseParams = {
        sequential: 1,
        return: caseReturnParams,
        options: {
          sort: 'modified_date DESC',
          limit: page.size,
          offset: page.size * (page.num - 1)
        }
      };
      var params = { 'case_type_id.is_active': 1 };

      return {
        cases: ['Case', 'getcaselist', $.extend(true, returnCaseParams, filter, params)],
        count: ['Case', 'getdetailscount', $.extend(true, returnCaseParams, filter, params)]
      };*/
    }


    /**
     * Fetches count of all the cases a contact have
     *
     * @param {Array|object} apiCall the api call parameters to use for counting cases
     */
    function getTotalThreadsCount (apiCall) {
      /*var count = 0;

      crmApi(apiCall).then(function (response) {
        _.each(response, function (ind) {
          count += ind;
        });

        $scope.totalCount = count;
      });*/
    }

    /**
     * Extends casesListConfig
     */
    function initMailutilsConfig () {
      /*_.each($scope.casesListConfig, function (item, ind) {
        $scope.casesListConfig[ind].cases = [];
        $scope.casesListConfig[ind] = $.extend(true, $scope.casesListConfig[ind], commonConfigs);
      });*/
    }

    /**
     * Subscribers for events
     */
    function initSubscribers () {
      $scope.$on('mailutils::contact-record-list::load-more', contactRecordListLoadmoreWatcher);
      $scope.$on('mailutils::contact-record-list::view-case', contactRecordListViewCaseWatcher);
    }

    /**
     * Loads additional data for contacts and set the first case as selected
     */
    function loadAdditionalDataWhenAllCasesLoaded () {
      /*if (isAllCasesLoaded()) {
        var allCases = _.reduce($scope.casesListConfig, function (memoriser, caseObj) {
          return memoriser.concat(caseObj.cases);
        }, []);

        fetchContactsData(allCases);

        if (!$scope.selectedCase) {
          setCaseAsSelected(allCases[0]);
          $scope.caseDetailsLoaded = true;
        }
      }*/
    }

    /**
     * Sets passed thread object as selected thread
     *
     * @param {object} thread the data belonging to a thread
     */
    function setThreadAsSelected (thread) {
      $scope.selectedThread = thread;
    }

    /**
     * Watcher function for thread collections
     *
     * @returns {boolean} true when all threads list have been loaded
     */
    function isAllThreadsLoaded () {
      /*return _.reduce($scope.casesListConfig, function (memoriser, data) {
        return memoriser && data.isLoaded;
      }, true);*/
    }

    /**
     * Updates the list with new entries
     *
     * @param {string} threadListIndex the case list config index to update
     * @param {Array} params the parameters to use when updating the thread list
     */
    function updateThread (threadListIndex, params) {
      /*crmApi(params).then(function (response) {
        _.each(response.cases.values, function (item) {
          item.contact_role = getContactRole(item);
          $scope.casesListConfig[caseListIndex].cases.push(formatCase(item));
        });

        $scope.casesListConfig[caseListIndex].isLoaded = true;
        $scope.casesListConfig[caseListIndex].showSpinner = false;
        $scope.casesListConfig[caseListIndex].isLoadMoreAvailable = $scope.casesListConfig[caseListIndex].cases.length < response.count;

        if ($scope.casesListConfig[caseListIndex].page.num === 1) {
          loadAdditionalDataWhenAllCasesLoaded();
        }

        $scope.casesListConfig[caseListIndex].page.num += 1;
      });*/
    }
  }

  module.filter('trustAsHtml',['$sce', function($sce) {
    return function (text) {
      return $sce.trustAsHtml(text);
    };
  }]);

})(angular, CRM.$, CRM._);
