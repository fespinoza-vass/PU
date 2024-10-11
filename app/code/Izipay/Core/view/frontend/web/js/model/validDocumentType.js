define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';

        return {

            /**
             * Validate checkout agreements
             *
             * @returns {Boolean}
             */
            validate: function () {
                var documentTypeValidationResult = true;
                var document_type = $('[name="izipay_document_type"]').val();
                var razon_social_izipay = $('[name="izipay_razon_social"]').val();
        
                if (document_type) {
                    var document_number = $('[name="izipay_document_number"]').val();
                    if (document_number.length == 0 ) {
                        $(".error-validation-izipay-document-number").text("Debe ingresar un número de documento.");
                        $(".error-validation-izipay-document-number").css("display", "block");
                        documentTypeValidationResult=false;
                    } else {
                        if (document_type == "DNI") {  
                            var regex = /^[0-9]{8}$/;
                            if (regex.test(document_number)) {
                                $(".error-validation-izipay-document-number").css("display", "none");
                                documentTypeValidationResult=true;
                            } else {
                                $(".error-validation-izipay-document-number").text("Debe ingresar 8 dígitos");
                                $(".error-validation-izipay-document-number").css("display", "block");
                                documentTypeValidationResult=false;
                            }
                        }
                        if (document_type == "RUC") {
                            var regex = /^[0-9]{11}$/;
                            if (regex.test(document_number)) {
                                $(".error-validation-izipay-document-number").css("display", "none");
                                if (razon_social_izipay.trim() != "") {
                                    documentTypeValidationResult=true;
                                } else {
                                    $(".error-validation-izipay-document-number").text("Debe ingresar una razón social");
                                    $(".error-validation-izipay-document-number").css("display", "block");
                                    documentTypeValidationResult=false;
                                }
                            } else {
                                $(".error-validation-izipay-document-number").text("Debe ingresar 11 dígitos");
                                $(".error-validation-izipay-document-number").css("display", "block");
                                documentTypeValidationResult=false;
                            }
                        }
                        if (document_type == "CE") {
                            var regex = /^[a-zA-Z0-9]{0,12}$/;
                            if (regex.test(document_number)) {
                                $(".error-validation-izipay-document-number").css("display", "none");
                                documentTypeValidationResult=true;
                            } else {
                                $(".error-validation-izipay-document-number").text("Debe ingresar un dato alfanumérico de hasta 12 caracteres.");
                                $(".error-validation-izipay-document-number").css("display", "block");
                                documentTypeValidationResult=false;
                            }
                        }
                    }
                }

                return documentTypeValidationResult;
            }
        };
    }
);