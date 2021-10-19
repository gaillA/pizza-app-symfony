console.log("ça marche :)");

$(document).ready(function () {
  $(".sidenav").sidenav();
  if (typeof toastHTML !== "undefined") {
    M.toast({ html: toastHTML, classes: "red darken-3" });
  }
  if (typeof data !== "undefined") {
    $("input.autocomplete").autocomplete({ data });
  }
});
