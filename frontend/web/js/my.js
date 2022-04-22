/**
 * Created by mihail on 03.05.2017.
 */
/*Появление и исчезновение выпадающего списка акции для роли Руководитель*/
$( document ).ready(function() {
    var optionValue = $('#dealer').val();
    if(optionValue > 0){
        $('#action_name_dealer').show();
    }else{
        $('#action_name_dealer').hide();
    }
});
