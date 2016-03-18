var eduBookingView = {
	Customer: null,
	ContactPerson: null,
	Participants: [],
	MaxParticipants: 0,
	CurrentParticipants: 0,
	AddParticipant: function() {

		if(eduBookingView.MaxParticipants == -1 || eduBookingView.CurrentParticipants < eduBookingView.MaxParticipants)
		{
			var holder = document.getElementById('edu-participantHolder');
			var tmpl = document.querySelector('.eduadmin .participantItem.template');
			var cloned = tmpl.cloneNode(true);
			cloned.style.display = 'block';
			cloned.className = cloned.className.replace(' template', '');
			holder.appendChild(cloned);
		}
		else
		{
			var partWarning = document.getElementById('edu-warning-participants');
			if(partWarning)
			{
				partWarning.style.display = 'block';
				setTimeout(function() {
					var partWarning = document.getElementById('edu-warning-participants');
					partWarning.style.display = '';
				}, 5000);
			}
		}

		eduBookingView.UpdatePrice();
	},
	RemoveParticipant: function(obj) {
		var participantHolder = document.getElementById('edu-participantHolder');
		participantHolder.removeChild(obj.parentNode.parentNode);
		eduBookingView.UpdatePrice();
	},
	CheckParticipantCount: function()
	{
		var participants = document.querySelectorAll('.eduadmin .participantItem').length - 1;
		if(participants >= eduBookingView.MaxParticipants && eduBookingView.MaxParticipants >= 0) {
			return false;
		}
		return true;
	},
	UpdatePrice: function() {
		var contactParticipant = document.getElementById('contactIsAlsoParticipant');
		var contact = 0;
		if(contactParticipant) {
			if(contactParticipant.checked) {
				contact = 1;
			} else {
				contact = 0;
			}
		}

		eduBookingView.CurrentParticipants = document.querySelectorAll('.eduadmin .participantItem').length - 1 + contact;

		var priceObject = document.getElementById('sumValue');
		if(priceObject && pricePerParticipant && currency != '') {
			var newPrice = eduBookingView.CurrentParticipants * pricePerParticipant;
			priceObject.innerText = numberWithSeparator(newPrice, ' ') + ' ' + currency;
		}
	},
	UpdateInvoiceCustomer: function() {
		var invoiceView = document.getElementById('invoiceView');
		if(invoiceView) {
			invoiceView.style.display = invoiceView.style.display == 'block' ? 'none' : 'block';
		}
	},
	CheckValidation: function() {
		var terms = document.getElementById('confirmTerms');
		if(terms) {
			if(!terms.checked)
			{
				var termWarning = document.getElementById('edu-warning-terms');
				if(termWarning)
				{
					termWarning.style.display = 'block';
					setTimeout(function() {
						var termWarning = document.getElementById('edu-warning-terms');
						termWarning.style.display = '';
					}, 5000);
				}
				return false;
			}
		}

		var participants = document.querySelectorAll('.eduadmin .participantItem:not(.template)');
		console.log(participants);
		var requiredFieldsToCreateParticipants = [
			'participantFirstName[]'
		];
		for(var i = 0; i < participants.length; i++) {
			var participant = participants[i];
			var fields = participant.querySelectorAll('input');
			for(var f = 0; f < fields.length; f++) {
				if(requiredFieldsToCreateParticipants.indexOf(fields[f].name) >= 0) {

					if(fields[f].value.replace(/ /i, '') == '') {
						/* Show missing participant-name warning */

						var partWarning = document.getElementById('edu-warning-missing-participants');
						if(partWarning)
						{
							partWarning.style.display = 'block';
							setTimeout(function() {
								var partWarning = document.getElementById('edu-warning-missing-participants');
								partWarning.style.display = '';
							}, 5000);
						}
						return false;
					}
				}
			}
		}

		return true;
	}
};

var eduDetailView = {
	ShowAllEvents: function(filter, me) {
		me.parentNode.parentNode.removeChild(me.parentNode);
		jQuery('.showMoreHidden[data-groupid="' + filter + '"]').slideDown();
	}
};

function numberWithSeparator(x, sep) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, sep);
}

var oldonload = window.onload;
window.onload = function(e) {
	if(oldonload)
		oldonload();
};