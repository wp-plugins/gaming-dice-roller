function is_numeric (mixed_var) {
 	// from phpjs.org/functions/is_numeric:449
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: David
	// +   improved by: taith
	// +   bugfixed by: Tim de Koning
	// +   bugfixed by: WebDevHobo (http://webdevhobo.blogspot.com/)
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: is_numeric(186.31);
	// *     returns 1: true
	// *     example 2: is_numeric('Kevin van Zonneveld');
	// *     returns 2: false
	// *     example 3: is_numeric('+186.31e2');
	// *     returns 3: true
	// *     example 4: is_numeric('');
	// *     returns 4: false
	// *     example 4: is_numeric([]);
	// *     returns 4: false
	
	return (typeof(mixed_var) === 'number' || typeof(mixed_var) === 'string') && mixed_var !== '' && !isNaN(mixed_var);
}

document.observe("dom:loaded", function() {
	var roll_button = $("gaming_dice_roller_go");

	if(roll_button) {
		var die  = $("gaming_dice_roller_die");
		var pool = $("gaming_dice_roller_pool");

		roll_button.observe("click", function() {
			var pool_value = $F(pool);
			if(!is_numeric(pool_value) || pool_value <= 0) alert("You must enter a positive whole number of dice to roll.");
			else new Ajax.Updater($("gaming_dice_roller_results").down("p"), gaming_dice_roller_ajax.url, {
				parameters: { "pool": pool_value, "die": $F(die), "action": "general_roll" }
			});
		});
		
		die.observe("change", function() { 
			if($F(die) == 100) {
				pool.disabled = true;
				pool.value = 1;
			} else {
				pool.disabled = false;
			}
		});
	}
});