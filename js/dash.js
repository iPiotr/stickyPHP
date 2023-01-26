/**
 * It sends an ajax request to the server and then calls the loadNotes function when the request is
 * done.
 */
function init () {
  let a = $.ajax({
    url: "core/getNotes.php",
    type: "POST",
    dataType: "json",
  });
  a.done(loadNotes);
}

/**
 * It loops through the data object, creates a new note element, sets the position of the note, and
 * adds it to the page.
 */
function loadNotes (data) {
  for (let n in data) {
    let i = data[n];
    if (data.hasOwnProperty(n)) {
      let note = $('<div class="note" id="'+i["id"]+'"><p>'+i["text"]+'</p><div class="d">x</div></div>');
      note.css({
        top: i["y"] + "px",
        left: i["x"] + "px"
      }).show();
      addToPage(note);
    }    
  }
}

/* Setting a click event on the plus button. */
$('#plus').on('click', function (e) {
  let text = $('#textarea').val();
  if (text) {
    window.text = text;
    let a = $.ajax({
      url: "core/plus.php",
      type: "POST",
      dataType: "text",
      data: "text=" + text
    });
    a.done(plusSuccess);
  }
});

/**
 * It creates a new note, adds it to the page, and then adds it to the database.
 */
function plusSuccess(data) {
	let note = $('<div class="note" id="'+data+'"><p>'+window.text+'</p><div class="d">x</div></div>');
  addToPage(note);
}

/**
 * It takes the id of the note, the x and y coordinates of the note, and sends them to the update.php
 * file.
 */
function drop (e) {
  let note = $(e.target);
  let main = $("#main");
  let id = note.attr('id');
  let x = note.offset().left - main.offset().left;
  let y = note.offset().top - main.offset().top;
  let params = "id="+id+"&x="+x+"&y="+y;
  $.ajax({
    url: "core/update.php",
    type: "POST",
    dataType: "text",
    data: params
  });
}

/**
 * It deletes the note from the database
 */
function del (e) {
  let parent = $(e.target).parent();
  let id = parent.attr('id');
  parent.remove();
  $.ajax({
    url: "core/delete.php",
    type: "POST",
    dataType: "text",
    data: "id=" + id
  });
}

/**
 * It adds a note to the page, and makes it draggable.
 */
function addToPage(note) {
  $('#main').append(note);
  $('.d', note).on('click', del);
  note.draggable({ containment: "#main", scroll: false, stop: drop });
}

window.onload = init;
