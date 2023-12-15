$(".sidebar-toggle").click(function (event) {
  event.preventDefault();
  if (Boolean(sessionStorage.getItem("sidebar-toggle-collapsed"))) {
    sessionStorage.setItem("sidebar-toggle-collapsed", "");
  } else {
    sessionStorage.setItem("sidebar-toggle-collapsed", "1");
  }
});

$(document).on("keydown", "body", function (e) {
  if (e.ctrlKey && e.key === "/") {
    $(".searchButton").trigger("click");
  }
});
$(".searchButton").click(function () {
  $("#searchSection").fadeIn();
  $("#searchSection").addClass("active");
  $("#searchSection").css("opacity", "1");
});

$(".closeSearch").click(function () {
  $("#searchSection").fadeOut();
  $("#searchSection").removeClass("active");
  $("#searchSection").css("opacity", "0");
});

$(document).on("ifChanged", "input", function (event) {
  $(event.target).trigger("change");
});
$(document).on("click", "#show-buttons", function () {
  $(this).addClass("hidden");
  $(".form-group").show();
  $("#save_btn").show();
  $(".fa-minus-circle").show();
  $(".minus-header").each(function () {
    if ($(this).find(".minus-button").length == 0) {
      $(this).append('<i class = "fa fa-minus-circle"></i>');
    }
  });
  $("#cancel_btn").show();
});

var fadedColumns = [];
$(document).on("click", "#cancel_btn", function () {
  $(this).hide();
  $("#faded_columns").val("");
  $("#save_btn").hide();
  // $(".form-group").hide();
  $("#show-buttons").removeClass("hidden");
  $(".minus-header .fa").remove();
  $("td,th").css("opacity", "1");
  fadedColumns = [];
  $("#faded_columns").val("");
  $("#save_btn").addClass("disabled");
});

$(document).on("click", ".minus-header .fa", function () {
  var fadedColumns = [];
  if ($("#faded_columns").val() != "") {
    var fadedColumns = $("#faded_columns").val().split(",");
  }
  var columnIndex = $(this).closest("th").index();
  var columnAttribute = $(this).closest("th").data("attribute");
  // console.log(columnAttribute);
  if ($(this).hasClass("fa-minus-circle")) {
    $(this).removeClass("fa-minus-circle").addClass("fa-plus-circle");
    $(
      "td:nth-child(" +
        (columnIndex + 1) +
        "),th:nth-child(" +
        (columnIndex + 1) +
        ")"
    ).css("opacity", "0.5");
    $(
      "td:nth-child(" +
        (columnIndex + 1) +
        "),th:nth-child(" +
        (columnIndex + 1) +
        ")"
    ).css("pointer-events", "none");
    if (!fadedColumns.includes(columnAttribute)) {
      fadedColumns.push(columnAttribute);
    }
  } else if ($(this).hasClass("fa-plus-circle")) {
    $(this).removeClass("fa-plus-circle").addClass("fa-minus-circle");
    $(
      "td:nth-child(" +
        (columnIndex + 1) +
        "),th:nth-child(" +
        (columnIndex + 1) +
        ")"
    ).css("opacity", "1");
    $(
      "td:nth-child(" +
        (columnIndex + 1) +
        "),th:nth-child(" +
        (columnIndex + 1) +
        ")"
    ).css("pointer-events", "auto");
    var index = fadedColumns.indexOf(columnAttribute);
    if (index > -1) {
      fadedColumns.splice(index, 1);
    }
  }
  if (fadedColumns.length === 0) {
    $("#save_btn").addClass("disabled");
    $("#save_btn").prop("disabled", true);
    var updatedValue = "";
  } else {
    $("#save_btn").removeClass("disabled");
    $("#save_btn").prop("disabled", false);
    var updatedValue = fadedColumns.join(",");
  }
  // console.log(updatedValue);
  $("#faded_columns").val(updatedValue);
});
