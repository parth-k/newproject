$(document).ready(function() {
  $("#notify_table").dataTable({
    language: {
      paginate: {
        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
      },
      search: "",
      searchPlaceholder: "Search..."
    },
    dom: '<"wpsp-dataTable-top"f>rt<"wpsp-dataTable-bottom"<"wpsp-length-info"li>p<"clear">>',
    order: [],
    columnDefs: [{
      targets: "nosort",
      orderable: !1
    }],
    drawCallback: function(e) {
      $(this).closest(".dataTables_wrapper").find(".dataTables_paginate").toggle(this.api().page.info().pages > 1)
    },
    responsive: !0
  }), $(document).on("click", ".notify-Delete", function() {
    $("#DeleteModal").css("display", "block"), $("#DeleteModal").addClass("wpsp-popVisible");
    var e = $(this).attr("data-id");
    $("#teacherid").val(e)
  }), $(document).on("click", ".ClassDeleteBt", function() {
    var e = $("#teacherid").val();
    if ("" == e);
    else {
      var t = [];
      t.push({
        name: "action",
        value: "deleteNotify"
      }, {
        name: "notifyid",
        value: e
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: t,
        beforeSend: function() {
          $("#DeleteModal").css("display", "none")
        },
        success: function(e) {
          location.reload(), $(this).closest("tr").remove()
        },
        error: function() {
          $("#WarningModal").html("Something went wrong. Try after refreshing page..")
        },
        complete: function() {
          $(".pnloader").remove()
        }
      })
    }
  }), $(document).on("click", ".notify-view", function() {
    var e = $(this).attr("data-id");
    if ($.isNumeric(e)) {
      var t = [];
      t.push({
        name: "action",
        value: "getNotify"
      }, {
        name: "notifyid",
        value: e
      }), $.ajax({
        type: "POST",
        url: ajax_url,
        data: t,
        beforeSend: function() {},
        success: function(e) {
          $("#ViewModalContent").html(e), $(this).click()
        },
        error: function() {
          $.fn.notify("error", {
            desc: "Something went wrong. Try after refreshing page.."
          })
        },
        complete: function() {
          $(".pnloader").remove()
        }
      })
    } else $.fn.notify("error", {
      desc: "Notification ID Missing.."
    })
  }), $(document).on("click", "#notifySubmit", function() {
    $("#NotifyEntryForm").validate({
      rules: {
        subject: {
          required: !0,
          minlength: 10
        },
        receiver: "required",
        type: "required"
      },
      submitHandler: function(e) {
        $("#notifySubmit").submit()
      }
    })
  }), $("#example-enableClickableOptGroups-disabled").multiselect({
    includeSelectAllOption: !0,
    enableFiltering: !0
  })
});
