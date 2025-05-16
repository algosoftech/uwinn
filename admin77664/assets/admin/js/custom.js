
  //integer value validation

 // erase cookies
function inputnumber() {
	$('input.floatNumber').on('input', function() {
    this.value = this.value.replace(/[^0-9.]/g,'').replace(/(\..*)\./g, '$1');
});
}