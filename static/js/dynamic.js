$(document).ready(function () {
  $(".delete-button").on("click", function () {
    var parentClass = $(this).data("parent-class");
    var parentId = $(this).data("parent-id");
    var childClass = $(this).data("child-class");
    var paramsAttribute = $(this).data("params-attribute");
    var childName = $(this).data("child-name");
    var parentName = $(this).data("parent-name");
    var deletedName = $(this).data("deleted-name");

    var urlPath = $(this).data("url-path");
    if (confirm("Are you sure you want to delete this item?")) {
      $.ajax({
        type: "POST",
        url: updateParentStatusUrl,
        data: {
          parentClass: parentClass,
          parentId: parentId,
          childClass: childClass,
          paramsAttribute: paramsAttribute,
          childName: childName,
        },
        success: function (response) {
          window.location.href =
            "../dynamic/move-child?parentClass=" +
            parentClass +
            "&childClass=" +
            childClass +
            "&paramsAttribute=" +
            paramsAttribute +
            "&parentId=" +
            parentId +
            "&childName=" +
            childName +
            "&parentName=" +
            parentName +
            "&deletedName=" +
            deletedName +
            "&urlPath=" +
            urlPath;
        },
        error: function () {},
      });
    }
  });
  $("#parent-dropdown").change(function () {
    var selectedParent = $(this).val();
    var moveButton = $('button[name="moveButton"]');
    if (selectedParent) {
      moveButton.prop("disabled", false);
    } else {
      moveButton.prop("disabled", true);
    }
  });
  var checkboxes = $("input[type=checkbox]");

  checkboxes.on("change", function () {
    var checkedCheckboxes = checkboxes.filter(":checked");
    var moveButton = $("#moveButton");
    var deleteButton = $("#deleteButton");
    //   moveButton.prop("disabled", checkedCheckboxes.length == 0);
    deleteButton.prop("disabled", checkedCheckboxes.length === 0);
  });
});
