$('#shareModal').on('show.bs.modal', function (e) {
    var button = e.relatedTarget;
    if (button != null)
    {
        alert("Launch Button ID='" + button.id + "'");
    }
});