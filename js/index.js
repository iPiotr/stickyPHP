/* This is a jQuery function that is called when the user clicks on a tab. It prevents the default
action of
the click, adds the class 'active' to the tab that was clicked, removes the class 'active' from the
other tabs, and then shows the content of the tab that was clicked. */
$('.form').find('input, textarea').on('keyup blur focus', function (e) {
  
  let $this = $(this),
      label = $this.prev('label');

	  if (e.type === 'keyup') {
			if ($this.val() === '') {
          label.removeClass('active highlight');
        } else {
          label.addClass('active highlight');
        }
    } else if (e.type === 'blur') {
    	if( $this.val() === '' ) {
    		label.removeClass('active highlight'); 
			} else {
		    label.removeClass('highlight');   
			}   
    } else if (e.type === 'focus') {
      
      if( $this.val() === '' ) {
    		label.removeClass('highlight'); 
			} 
      else if( $this.val() !== '' ) {
		    label.addClass('highlight');
			}
    }
});

/* A jQuery function that is called when the user clicks on a tab. It prevents the default action of
the click, adds the class 'active' to the tab that was clicked, removes the class 'active' from the
other tabs, and then shows the content of the tab that was clicked. */
$('.tab a').on('click', function (e) {
  
  e.preventDefault();
  
  $(this).parent().addClass('active');
  $(this).parent().siblings().removeClass('active');
  
  target = $(this).attr('href');

  $('.tab-content > div').not(target).hide();
  
  $(target).fadeIn(600);
  
});


/**
 * If the user is logged in, then click the second tab and check the username. Otherwise, check the
 * username in the first tab
 */
function init() {
  box1 = $( "input[name=usernameS]" );
  box2 = $( "input[name=usernameL]" );
  if (box2.val() != '') {
    $('.tab a')[1].click();
    box2.keyup();
  }
  else {
    box1.keyup();
  }
}
window.onload = init;