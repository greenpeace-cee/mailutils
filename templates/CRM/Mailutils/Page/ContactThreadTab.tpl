<div id="mailutilsContactThreadTab" >
  <div class="container" ng-view></div>
</div>
{literal}
  <script type="text/javascript">
    (function(angular, $, _) {
      angular.module('mailutilsContactThreadTab', ['mailutils']);
      angular.module('mailutilsContactThreadTab').config(function($routeProvider) {
        $routeProvider.when('/', {
          reloadOnSearch: false,
          template: '<mailutils-contact-thread-tab></mailutils-contact-thread-tab>'
        });
      });
    })(angular, CRM.$, CRM._);

    CRM.$(document).one('crmLoad', function(){
      angular.bootstrap(document.getElementById('mailutilsContactThreadTab'), ['mailutilsContactThreadTab']);
    });
  </script>
{/literal}
