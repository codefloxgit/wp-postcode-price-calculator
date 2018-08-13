var wppcpc=function(){
	this.init=function(){
		$=jQuery;
		wppcpc.calculate();
	};
	this.calculate=function(){
		$(document).on('click', '.wppcpc_pcpcbtn', function(e){
			postcode=$('.wppcpc_postcode').val();
			$.ajax({
				type:'POST',context:this,dataType:'json',url:wppcpcobj.ajaxurl,
				data:{'action':'wp_pcpc_ajax','act':'do_calculate','postcode':postcode},
				beforeSend:function(){swal({title:'',text:'calculating',allowOutsideClick:false,allowEscapeKey:false,allowEnterKey:false,onOpen:()=>{swal.showLoading()}});swal.showLoading()},
				success:function(data){
					if(data){
						if(data.status=='success'){
							distance 	=	parseInt(data.result.distance);
							price 		=	parseInt(data.result.price);
							currency 	=	data.result.currency;

							html=': <b>'+distance+'</b><br>' + 'Approximate Cost: <b>'+price+' '+currency+'</b>';

							r_distance 	=	'<label>Distance</label><input type="text" value="'+distance+'">';

							r_price 	=	'<label>Approximate Cost</label><span class="pcpc_cost"><i>'+currency+'</i> '+price+'</span>';

							$('.wppcpc_result_distance').html(r_distance);
							$('.wppcpc_result_cost').html(r_price);

							swal.close()

						/*	swal({
							title: '',
							type: 'info',
							html:
							'Approximate Distance: <b>'+distance+'</b><br>' +
							'Approximate Cost: <b>'+price+' '+currency+'</b>',
							showCloseButton: true,
							showCancelButton: false,
							focusConfirm: false,
							confirmButtonText:'Close',
							//confirmButtonAriaLabel: 'Thumbs up, great!',
							//cancelButtonText:'',
							//cancelButtonAriaLabel: 'Thumbs down',
							})*/
						}
						else{swal('',data.msg,'error')}
					}
				}
			})
		});

	};
};wppcpc=new wppcpc();wppcpc.init();