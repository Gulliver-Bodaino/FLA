function share_btn(num){
  var here = location.href;
  switch(num){
  case 'tw':
    var next ='http://twitter.com/share?url='+here+'&text='+$('p.title').text()+' ';
    window.open(next, 'TwitterWindow', 'width=650, height=450, menubar=no, toolbar=no, scrollbars=yes');
    return false;
    break;
  case 'fb':
    var next =	'http://www.facebook.com/share.php?u='+here;
    window.open(next, 'FBwindow', 'width=650, height=450, menubar=no, toolbar=no, scrollbars=yes');
    return false;
    break;
  case 'gp':
    var here_edit=here.replace("http://", "");
    var next='https://plus.google.com/share?url='+here_edit;
        window.open(next, 'GPwindow', 'width=650, height=450, menubar=no, toolbar=no, scrollbars=yes');
    return false;
    break;
  case 'li':
    var next='http://line.me/R/msg/text/?'+here;
      window.open(next, 'LineWindow', 'width=650, height=450, menubar=no, toolbar=no, scrollbars=yes');
      return false;
    break;
  }
}
