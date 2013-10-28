$(document).ready(function() {
	
	$('body').on('click', '.catUl > li > span', function () {
		catId = $(this).attr("cat-id");
		loadCategory(catId);
		
	});
	
    $("#searchBtn").click(function(){
    	if($("#searchBtnGly").hasClass("glyphicon-chevron-down")){
    		serachBtnDivOpen();
    		loadCategory(0);
    	}else{
    		serachBtnDivClose();
    	}
    });
    
    function loadCategory(categoryId){
    	//console.log(categoryId);
    	var ajaxurl  = makeUrl({module: "category", action: "searchBtnList"});
		$.ajax({
			url: ajaxurl,
            type: 'POST',
            data: {categoryId: categoryId},
            async:false,
			success: function(data){
            	
				aData = data.split("~~||~~");
				hasChild = 1;

				if(aData[3] == ""){
					nextLevel = parseInt(aData[2]) + parseInt(1);
					$(".cat-list-"+nextLevel).html("");
					hasChild = 0;
				}
				
				if(aData[0] > 0){
					if(hasChild){
						n = parseInt(aData[0]) + 1;
						for(i = n; i <= 4; i++){
							$(".cat-list-"+i).html("");
						}
					}
					
					$(".cat-list-"+aData[0]).html(aData[1]);
					
				}
				
				
			},
			error: function(){
				console.log('Error');
			}
		});
    }
    
    function serachBtnDivOpen(){
    	$("#searchBtnDiv").css("display","block");
    	$("#btn-inner").fadeIn("slow");
    	$("#searchBtnDiv")
    	.animate({ height: "300px" }, 100 )
        .animate({ width: "1000px" }, 200 );
    	$(".searchBtnInner").animate({backgroundColor: "#444444"}, 300);
    	
    	$("#searchBtnGly").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
    }
    
    function serachBtnDivClose(){
    	$("#searchBtnGly").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
    	$("#btn-inner").fadeOut("slow");
    	$(".searchBtnInner").animate({backgroundColor: "#f86d18"}, 300);
    	$("#searchBtnDiv")
    	.animate({ width: "0px" }, 200 )
    	.animate({ height: "0px" }, 100 )
    	$("#searchBtnDiv").fadeOut("fast");
    }
    
    
    
});
