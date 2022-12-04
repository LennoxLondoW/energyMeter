$(function () {

  //   ::-webkit-scrollbar {
  //     width: 0px;
  //     display: none !important;
  // }

  // /* Track */
  // ::-webkit-scrollbar-track {
  //     box-shadow: inset 0 0 1px transparent;
  //     border-radius: 10px;
  // }

  // /* Handle */
  // ::-webkit-scrollbar-thumb {
  //     background: rgba(155, 155, 155, .4);
  //     border-radius: 10px;
  // }









  $("body").on("mouseover", "*", function () {
    $(this).css({
      '-ms-overflow-style': 'auto',
      'scrollbar-width': 'thin'

    }).addClass('show_bar');
  });

  $("body").on("mouseleave", "*", function () {
    $(this).css({
      '-ms-overflow-style': 'none',
      'scrollbar-width': 'none'

    }).removeClass('show_bar');
  })

  $("*").on("mouseover", function () {
    $(this).css({
      '-ms-overflow-style': 'auto',
      'scrollbar-width': 'thin'

    }).addClass('show_bar');
  });

  $("*").on("mouseleave", function () {
    $(this).css({
      '-ms-overflow-style': 'none',
      'scrollbar-width': 'none'

    }).removeClass('show_bar');
  })

  // submiting ajax forms
  $("body").on("submit", "form.edit, form.ajax", function (e) {
    e.preventDefault();
    var clas = $(this).hasClass("ajax") ? "ajax" : "edit";
    var form = $(this),
      // submit button
      button = form.find('[type="submit"]:first'),
      value = button.val(),
      form_data = new FormData();

    if (value === undefined || value === "") {
      value = button.html();
      //collect inputs
    }
    form.find("[name]").each(function () {
      var input = $(this);
      if (input.attr("type") === "file") {
        /*uploading files*/
        $.each(input.prop("files"), function (i, file) {
          form_data.append(input.attr('name'), file);
        });
      }
      else {
        form_data.append(input.attr('name'), input.val());
      }
    });


    button.prop('disabled', true).val('...').html('...');
    form.addClass('non_edit').removeClass(clas);
    //throw to backend
    $.ajax({
      url: form.attr("action"),
      type: "POST",
      timeout: 60000,
      data: form_data,
      contentType: false,
      processData: false,
      success: function (result) {
        button.prop('disabled', false).val(value).html(value);
        form.removeClass('non_edit').addClass(clas);
        //execute backend result
        console.log(result);
        eval(result);
      },
      error: function (a, b, c) {
        button.prop('disabled', false).val(value);
        form.removeClass('non_edit').addClass(clas);
        alert('Something is not right');
      }
    });
    return false;

  });

  // submiting non-ajax forms -> dont submit
  $("body").on("submit", "form.non_edit", function (e) {
    e.preventDefault();
    return false;
  });

  $("body").on("submit", "form.delete_element", function (e) {
    e.preventDefault();
    Swal.fire({
      icon: 'info',
      html: '<p>Do you want to delete this element?</p>',
      showCancelButton: true,
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel'
    }).then((result) => {

      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        $(this).removeClass('delete_element').trigger("submit");
        Swal.close();
        return false;
      }
      else {
        Swal.close();
        return false;
      }
    });
  });


  // editing content
  $("body").on("click", "[data-editable='true']", function (e) {
    var parent = $(this).parents(".client-single");
    if (parent.html() !== undefined && parent.hasClass("inactive")) {
      return true;
    }
    e.preventDefault();
    var type = $(this).attr('data-type');
    var table = $(this).attr('data-table');
    var section_id = $(this).attr('data-section_id');
    var title = type === 'image' ? "Upload new image" : "Edit text for this section";
    var tag = $(this).prop("tagName").toLowerCase();
    tag = $(this).parents("p").html() === undefined && !$(this).hasClass('non_ck') ? tag : 'p';

    if ($(this).hasClass("by_pass")) {
      $.ajax({
        url: "?",
        type: "POST",
        timeout: 60000,
        data: {
          'section_id': section_id,
          'table': table,
          'edit_element': 'edit_element',
          'section_title': $(this).attr("data-section_title"),
        },
        success: function (result) {
          eval(result);
        },
        error: function (a, b, c) {
          alert('Something is not right');
        }
      });

      return;
    }

    var form = `
      <form method='post' class='edit' action='?' enctype='multipart/form-data'>
        <input type= 'hidden' value='`+ section_id + `' name='section_id'>
        <input type= 'hidden' value='`+ table + `' name='table'>
        <div class='form-group'>
                `+
      (type === 'image' ?
        `<input  required type='file' name='file' class=''>` :
        (
          section_id.indexOf('_background') === -1 && section_id.indexOf('_color') === -1 ?
            `<textarea required id="editor"   rows='5'  name='section_title' class='form-control'>` + (tag === 'p' ? $(this).text() : $(this).html()) + `</textarea>` :
            `<input required type="color" name='section_title' value="` + ($(this).text()) + `" class='form-control'>`
        )
      )
      + `
        </div>
        <br>
        <div class='form-group'>
                <input type='submit'  style='padding:10px; border-radius:5px; border:none; outline:none; background:#09c; color:#fff;' name='edit_element' value='save and publish'>
        </div>
      </form>
  `;

    Swal.fire({
      title: title,
      html: form,
      customClass: 'swal-wide',
      confirmButtonText: 'close'
    });

    if (type !== "image" && tag !== 'p') {
      setTimeout(() => {
        var editor = CKEDITOR.replace('section_title');
        editor.on('instanceReady', function (event) {
          if (event.editor.getCommand('maximize').state == CKEDITOR.TRISTATE_OFF) {
            //ckeck if maximize is off
            event.editor.execCommand('maximize');
          }
        });


        // createEditor();
      }, 500);
    }
    return false;
  });
  // mark editable elements
  $('[data-editable="true"]').addClass('editable');

});
