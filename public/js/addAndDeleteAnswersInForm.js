let $collectionHolder;
let $newLinkLi = $('<li></li>');

document.getElementById('add').addEventListener('click', function() {
    addNewForm($collectionHolder, $newLinkLi);
});

jQuery(document).ready(function() {
    $collectionHolder = $('ul.collections');
    $collectionHolder.find('li').each(function() {
        addDeleteLink($(this));
    });
    $collectionHolder.append($newLinkLi);
    $collectionHolder.data('index', $collectionHolder.find('input').length);

});

function addNewForm($collectionHolder, $newLinkLi) {
    let $prototype=$collectionHolder.data('prototype');
    let index = $collectionHolder.data('index');
    let $newLiElement=$('<div class="form-group row"> <div class="form-check"> <input class="form-check-input position-static" ' +
        'type="radio" name="isTrue" id="isTrue" value="'+index+'"> </div><div class="input-group-append"> </div> </div>');
    let $newForm = $prototype;
    $newForm = $newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    $newForm=$newLiElement.append($newForm);
    let $newFormLi = $('<li></li>').append($newForm);
    $newFormLi=addDeleteLink($newFormLi);
    $newLinkLi.before($newFormLi);
}

function addDeleteLink($formLi) {
    let $removeFormButton = $('<div class="form-group row"><button type="button"  class="btn btn-outline-danger">' +
        '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" ' +
        'xmlns="http://www.w3.org/2000/svg"> <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 ' +
        '.5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>' +
        '<path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0' +
        ' 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 ' +
        '0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>' +
        '</svg></button></div>');
    $formLi=$formLi.append($removeFormButton);
    $removeFormButton.on('click', function() {
        $formLi.remove();
    });
    return $formLi;
}