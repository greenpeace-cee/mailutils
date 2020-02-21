(function (angular) {
  var module = angular.module('mailutils');

  /**
   * Directive for the thread list sortable headers
   */
  module.directive('mailutilsThreadListSortHeader', function () {
    return {
      restrict: 'A',
      link: mailutilsSortheaderLink
    };

    /**
     * Link function for mailutilsSortheaderLink Directive
     *
     * @param {object} scope
     * @param {object} $el
     * @param {object} attrs
     */
    function mailutilsSortheaderLink (scope, element, attrs) {
      (function init () {
        initiateSortFunctionality();
        scope.$watchCollection('sort', sortWatchHandler);
      }());

      /**
       * Initiate the sort functionality if the header is sortable
       */
      function initiateSortFunctionality () {
        if (scope.sort.sortable && attrs.mailutilsThreadListSortHeader !== '') {
          element
            .addClass('mailutils__thread-list-sortable-header')
            .on('click', headerClickEventHandler);
        }
      }

      /**
       * Click event for the header
       * If the Clicked field is already selected, change the direction
       * Otherwise, set the new field and direction as ascending
       */
      function headerClickEventHandler () {
        scope.$apply(function () {
          if (scope.sort.field === attrs.mailutilsThreadListSortHeader) {
            scope.changeSortDir();
          } else {
            scope.sort.field = attrs.mailutilsThreadListSortHeader;
            scope.sort.dir = 'ASC';
          }
        });
      }

      /**
       * Watch event for the Sort property
       */
      function sortWatchHandler () {
        element.toggleClass('active', attrs.mailutilsThreadListSortHeader === scope.sort.field);
        element.find('.mailutils__thread-list__header-toggle-sort').remove();

        if (attrs.mailutilsThreadListSortHeader === scope.sort.field) {
          var direction = scope.sort.dir === 'ASC' ? 'up' : 'down';
          var sortIcon = '<i class="mailutils__thread-list__header-toggle-sort material-icons">arrow_' + direction + 'ward</i>';
          element.append(sortIcon);
        }
      }
    }
  });
})(angular);
