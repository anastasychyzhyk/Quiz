jQuery(document).ready(function() {
    $('#save').on('click', function (event) {
        let $errors=$('#errors');
        $errors.empty();
        if($('#isTrue').length===0) {
            return ;
        }
        if(!$("input[name='isTrue']:checked").val()) {
            $errors.append($('<small class="form-text text-danger">{% trans %}Select right answer{% endtrans %}</small>'));
            event.preventDefault();
        }
    });
});