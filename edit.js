let typebox = document.getElementById('typebox')
moveSelection(typebox);

function moveSelection(element)
{
    element.focus();
    element.setSelectionRange(element.value.length,element.value.length);
}