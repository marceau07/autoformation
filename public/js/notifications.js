// On DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
  //Open dropdown when clicking on element
  $(document).on('click', "a[data-dropdown='notificationMenu']", handleClickDropdown);

  //Close dropdowns on document click
  $(document).on('click', '#dropdownOverlay', handleClickDocumentCloseDropdown);

  //Dropdown collapsile tabs
  $('.notification-tab').click(handleNotificationTabClick);

});

handleClickDropdown = function (e) {
  e.preventDefault();

  var el = $(e.currentTarget);

  var container = $(e.currentTarget).parent();
  container.toggleClass('expanded')
  if (container.hasClass('expanded')) {
    $('#dropdownOverlay').remove();
    $('body').prepend('<div id="dropdownOverlay" style="background: transparent; height:100%;width:100%;position:fixed;"></div>')
  }

  var dropdown = container.find('.dropdown');
  var containerWidth = container.width();
  var containerHeight = container.height();

  var anchorOffset = $(e.currentTarget).offset();

  dropdown.css({
    'right': containerWidth / 2 + 'rem',
    'top': containerWidth / 1.8 + 'rem'
  });
};

handleClickDocumentCloseDropdown = function (e) {
  var el = $(e.currentTarget)[0].activeElement;

  if (typeof $(el).attr('data-dropdown') === 'undefined') {
    $('#dropdownOverlay').remove();
    $('.dropdown-container.expanded').removeClass('expanded');
  }
};

handleNotificationTabClick = function (e) {
  if ($(e.currentTarget).parent().hasClass('expanded')) {
    $('.notification-group').removeClass('expanded');
  }
  else {
    $(e.currentTarget).parent().toggleClass('expanded');
  }
}