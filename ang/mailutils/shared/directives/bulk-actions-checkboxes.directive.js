(function (angular, $, _) {
  var module = angular.module('mailutils');

  module.directive('mailutilsBulkActionsCheckboxes', function () {
    return {
      restrict: 'EA',
      controller: 'mailutilsBulkActionsController',
      templateUrl: '~/mailutils/shared/directives/bulk-actions-checkboxes.directive.html',
      scope: {
        showCheckboxes: '=?',
        selectedItems: '=',
        isSelectAllAvailable: '=',
        everythingCount: '=',
        displayedCount: '='
      }
    };
  });

  module.controller('mailutilsBulkActionsController', function ($scope) {
    $scope.showCheckboxes = false;

    (function init () {
      initWatchers();
    }());

    /**
     * Toggle checkbox states
     */
    $scope.toggleCheckbox = function () {
      $scope.showCheckboxes = !$scope.showCheckboxes;
    };

    /**
     * Emits event for bulk selections
     * Available event
     * - 'all' : Select all that matches the search
     * - 'visible' :  Selects all visible selections
     * - 'none' : Deselects all
     *
     * @params {String} condition
     */
    $scope.select = function (condition) {
      $scope.$emit('mailutils::bulk-actions::bulk-selections', condition);
    };

    /**
     * Intiate watchers for this controller
     */
    function initWatchers () {
      $scope.$watch('selectedItems', selectedItemsWatcher);
    }

    /**
     * selectedItems variable watcher
     */
    function selectedItemsWatcher () {
      $scope.$emit('mailutils::bulk-actions::bulk-message-toggle');
    }
  });
})(angular, CRM.$, CRM._);
