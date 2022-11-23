define(
    [
        'jquery',
        'Magento_Ui/js/model/messageList',
        'mage/validation'
    ],
    function ($,messageList){
        'use strict'
        return{
            validate:function (){
                var checkboxPromotion=$("#receive-promotion").val();
                var  validation = false;

                if(checkboxPromotion == 1){
                    validation=true;
                }
                else{
                    messageList.addErrorMessage({message:"Publicidad y promociones es un campo obligatorio."})
                }

                return validation;
            }
        }
    }
)
