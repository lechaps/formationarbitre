function AddQuestionToTest(idQuestion, idTest) {
	var note 	= $("#note_"+idTest).val();
	var notation= $("#notation_"+idTest).val();
	$.ajax({
		url: 'Q_Tadd.php',
        type: 'POST',
        data: 'idquestion='+idQuestion+'&idtest='+idTest+'&note='+note+'&notation='+notation,
        success: function (html) {
            var obj = jQuery.parseJSON(html);
            $('#myModal').removeData('bs.modal');
            $('.modal-content').load('Q_modal.php?idquestion='+idQuestion+'&resultat='+obj.result+'&message='+obj.message);
            var Nbtest=parseInt($('#nbTest'+idQuestion).text())+1;
            $('#nbTest'+idQuestion).text(Nbtest);
        }
    });
}

function RemoveQuestionToTest(idQuestion, idTest) {
	$.ajax({
		url: 'Q_Trem.php',
        type: 'POST',
        data: 'idquestion='+idQuestion+'&idtest='+idTest,
        success: function (html) {
            var obj = jQuery.parseJSON(html);
            $('#myModal').removeData('bs.modal');
            $('.modal-content').load('Q_modal.php?idquestion='+idQuestion+'&resultat='+obj.result+'&message='+obj.message);
            if($('#nbTest'+idQuestion).length)  { 
            	var Nbtest=parseInt($('#nbTest'+idQuestion).text())-1;
            	$('#nbTest'+idQuestion).text(Nbtest);
             }
        }
    });
}

function SetValidatorQuestion(idQuestion, val) {
	$.ajax({
		url: 'Q_val.php',
        type: 'POST',
        data: 'idquestion='+idQuestion+'&val='+val,
        success: function (html) {
            var obj = jQuery.parseJSON(html);
            $('.modal-content').load('Q_modal.php?idquestion='+idQuestion+'&resultat='+obj.result+'&message='+obj.message);
            if($('#Q'+idQuestion+'_Status').length)  { 
            	$('#Q'+idQuestion+'_Status').attr('class', 'label label-'+CssStatut_tab[val]);
            	$('#Q'+idQuestion+'_Status').text(LbStatut_tab[val][1]);
            }
        }
    });
}

function DeleteQuestion(idQuestion) {
	$.ajax({
		url: 'Q_rem.php',
        type: 'POST',
        data: 'idquestion='+idQuestion,
        success: function (html) {
            var obj = jQuery.parseJSON(html);
            if (obj.result=='QDE')  { 
        	    if($('#Q'+idQuestion+'_Line').length)  { 
    	        	$('#Q'+idQuestion+'_Line').fadeOut('slow', function() {
	          			$(this).remove();
        			});
        		}
        	}
        	else alert(obj.message);
        	$('#myModal').modal('toggle')
        }
    });
}

function UpdateQuestion(idQuestion, libelle, champ) {
	$.ajax({
		url: 'Q_mod.php',
        type: 'POST',
        data: 'idquestion='+idQuestion+'&libelle='+libelle+'&champ='+champ,
        success: function (html) {
            var obj = jQuery.parseJSON(html);
            $('#myModal').removeData('bs.modal');
            $('.modal-content').load('Q_modal.php?idquestion='+idQuestion+'&resultat='+obj.result+'&message='+obj.message);
        }
    });
}


function changeNotation(id) {
	var note=$('#note_'+id).val();
	var s=''
	if (note>1) s='s';
	$('#notation_'+id).val(note + ' point'+s+' pour la réponse');
}

