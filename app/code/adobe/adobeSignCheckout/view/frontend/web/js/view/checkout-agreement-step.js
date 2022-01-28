define(
  ['jquery',
   'ko',
   'uiComponent',
   'underscore',
   'Magento_Checkout/js/model/step-navigator',
   'Magento_Ui/js/model/messageList'
  ],
  function (
    $,
    ko,
    Component,
    _,
    stepNavigator,
    messageList
  ) {
      'use strict';

      function showStep() {
          const quoteData = window.checkoutConfig.quoteData;
          if (quoteData['agreement_id']) {
              return true;
          } else {
              return false;
          }
      }

      /**
       * sign-agreement - is the name of the component's .html template
       */
      return Component.extend({
          defaults: {
              template: 'adobe_adobeSignCheckout/sign-agreement'
          },

          //add here your logic to display step,
          isVisible: ko.observable(showStep()),
          //step code will be used as step content id in the component template
          stepCode: 'signAgreement',
          //step title value
          stepTitle: 'Sign Agreement',

          signUrl: ko.observable(),
          hasSigned: ko.observable(false),

          /**
           *
           * @returns {*}
           */
          initialize: function () {
              this._super();

              if (showStep()) {
                  // register your step
                  stepNavigator.registerStep(
                      this.stepCode,
                      //step alias
                      null,
                      this.stepTitle,
                      //observable property with logic when display step or hide step
                      this.isVisible,

                      _.bind(this.navigate, this),

                      /**
                       * sort order value
                       * 'sort order value' < 10: step displays before shipping step;
                       * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                       * 'sort order value' > 20 : step displays after payment step
                       */
                      15
                  );
              }

              return this;
          },

          /**
           * The navigate() method is responsible for navigation between checkout step
           * during checkout. You can add custom logic, for example some conditions
           * for switching to your custom step
           */
          navigate: function () {
              this.isVisible(true);
          },

          /**
           * @returns void
           */
          navigateToNextStep: function () {
              stepNavigator.next();
          },

          checkStatus: function(self) {
              $.ajax({
                  url: '/adobeSignCheckout/quote/AgreementStatus?id=' + window.checkoutConfig.quoteData.entity_id,
                  type: 'GET',
                  dataType: 'json',
                  success: function (data) {
                      if (data['status']) {
                          self.hasSigned(true);
                      } else {
                          setTimeout(self.checkStatus, 1000, self)
                      }
                  },
                  error: function (xhr, status, error) {
                      let errorMessage = xhr.status + ': ' + xhr.statusText;
                      console.log('Error - ' + errorMessage);
                      messageList.addErrorMessage({message: 'Failed to get the agreement status.'});
                  }
              });
          },

          sign: function () {
              $('body').trigger('processStart');
              let self = this;
              $.ajax({
                  url: '/adobeSignCheckout/quote/agreement?id=' + window.checkoutConfig.quoteData.entity_id,
                  type: 'POST',
                  dataType: 'json',
                  success: function (data) {
                      setTimeout(function () {
                          $('body').trigger('processStop');
                          self.checkStatus(self);
                      }, 3000);
                      let signUrl = data['signUrl'];
                      signUrl += signUrl.indexOf('?') > 0 ? '&' : '?';
                      signUrl += 'noChrome=true';
                      self.signUrl(signUrl);
                  },
                  error: function (xhr, status, error) {
                      $('body').trigger('processStop');
                      let errorMessage = xhr.status + ': ' + xhr.statusText;
                      console.log('Error - ' + errorMessage);
                      messageList.addErrorMessage({message: 'Failed to create agreement.'});
                  }
              });
          }
      });
  }
);
