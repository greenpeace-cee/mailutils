(function (angular) {
  var module = angular.module('mailutils');
  var $ = angular.element;

  /**
   * Directive for the sticky pager footer functionality on case list page
   */
  module.directive('civicaseStickyFooterPager', function ($window, $timeout) {
    return {
      restrict: 'A',
      link: civicaseStickyFooterPagerLink
    };

    /**
     * Link function for stickyFooterPager Directive
     *
     * @param {object} scope
     *   Scope under which directive is called
     * @param {object} $el
     *   Element on which directive is called
     * @param {object} attrs
     *   attributes of directive
     */
    function civicaseStickyFooterPagerLink (scope, $el, attrs) {
      (function init () {
        scope.$watch('isLoading', checkIfLoadingCompleted);
      }());

      /**
       * Checks if loading completes and add logic
       * for fixed footer
       *
       * @param {boolean} loading
       */
      function checkIfLoadingCompleted (loading) {
        if (!loading) {
          var topPos;

          // $timeout is required to wait for the UI rendering to complete,
          // to get the correct offset of the element.
          $timeout(function () {
            topPos = $el.offset().top;
            applyFixedPager(topPos);
          });

          $($window).scroll(function () {
            applyFixedPager(topPos);
          });
        } else {
          $el.removeClass('civicase__pager--fixed');
        }
      }

      /**
       * Applies fixed pager class based on scroll position
       *
       * @param {int} topPos
       */
      function applyFixedPager (topPos) {
        if ((topPos - $($window).height() - $($window).scrollTop()) > 0) {
          $el.addClass('civicase__pager--fixed');
        } else {
          $el.removeClass('civicase__pager--fixed');
        }
      }
    }
  });
})(angular);
