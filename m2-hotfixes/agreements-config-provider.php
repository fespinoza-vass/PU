diff --git a/vendor/magento/module-checkout-agreements/Model/AgreementsConfigProvider.php b/vendor/magento/module-checkout-agreements/Model/AgreementsConfigProvider.php
index ff77db6..e2e96ca 100644
--- a/vendor/magento/module-checkout-agreements/Model/AgreementsConfigProvider.php
+++ b/vendor/magento/module-checkout-agreements/Model/AgreementsConfigProvider.php
@@ -100,7 +100,7 @@ class AgreementsConfigProvider implements ConfigProviderInterface
                 'content' => $agreement->getIsHtml()
                     ? $agreement->getContent()
                     : nl2br($this->escaper->escapeHtml($agreement->getContent())),
-                'checkboxText' => $this->escaper->escapeHtml($agreement->getCheckboxText()),
+                'checkboxText' => $agreement->getCheckboxText(),
                 'mode' => $agreement->getMode(),
                 'agreementId' => $agreement->getAgreementId(),
                 'contentHeight' => $agreement->getContentHeight()
