$(document).ready(function() {
	
	/*
	$(document).on("click", ".checkAll", function(){
		$(this).closest('fieldset').find(':checkbox').prop('checked', this.checked);
	}
	*/
	
	$("body").click(function(){
		searchBtnDivClose();
		searchTxtDivClose();
		//brandListClose();
	});
	
	
	$("#groupListBtn").click(function(e){
		if($('#groupListBtn span').hasClass('glyphicon-chevron-down')){
			//e.stopPropagation();
			catListOpen("groupList");
			loadCatList("groupList",2);
		}else{
			catListClose("groupList");
		}
	});
	
	
	$("#brandListBtn").click(function(e){
		if($('#brandListBtn span').hasClass('glyphicon-chevron-down')){
			//e.stopPropagation();
			//brandListOpen();
			catListOpen("brandList");
			loadCatList("brandList",4);
		}else{
			catListClose("brandList");
		}
	});
	
	function loadCatList(levelName, levelId){
		var ajaxurl  = makeUrl({module: "category", action: "laodCatList"});
		$.ajax({
			url: ajaxurl,
            type: 'POST',
            data: {levelId: levelId},
            async:false,
			success: function(data){
            		$("#"+levelName+"Inner").html(data);
            },
			error: function(){
				console.log('Error');
			}
		});
	}
	
	function catListClose(catId){
		console.log("#"+catId+"Btn");
    	$("#"+catId+"Btn")
  	    .animate({
  	        borderTopLeftRadius: 0, 
  	        borderTopRightRadius: 0}, "normal");
    	
    	$("#"+catId)
        	.animate({ height: "1px" }, "normal" );
    	
    	$("#"+catId+"Inner").fadeOut("normal");
    	$("#"+catId+"Btn").find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
    }
	
	function catListOpen(catId){
		console.log($("#"+catId+"Btn").html());
    	//$("#"+catId+"Btn").css("display","block");
    	
    	$("#"+catId)
        	.animate({ height: "300px" }, "normal" );
    	
    	$("#"+catId+"Btn")
    	  .animate({
    	    borderTopLeftRadius: 5, 
    	    borderTopRightRadius: 5}, "normal");
    	console.log("#"+catId+"Inner");
    	$("#"+catId+"Inner").fadeIn("normal");
    	$("#"+catId+"Btn").find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
    }
	
	$('body').on('click', '.catUl > li > span', function (e) {
		e.stopPropagation();
		catId = $(this).attr("cat-id");
		loadCategory(catId);
		
	});
	
	$("#searchTxtBox").keyup(function(e){
			searchTxtDivOpen();
			keys = $(this).val();
			searchCategory(keys);
	});
	
    $("#searchBtn").click(function(e){
    	e.stopPropagation();
    	if($("#searchBtnGly").hasClass("glyphicon-chevron-down")){
    		searchBtnDivOpen();
    		loadCategory(0);
    	}else{
    		searchBtnDivClose();
    	}
    });
    
    function searchCategory(keys){
    	var ajaxurl  = makeUrl({module: "category", action: "searchTxtList"});
		$.ajax({
			url: ajaxurl,
            type: 'POST',
            data: {keys: keys},
            async:false,
			success: function(data){
            	$("#searchTxtDiv").html(data);
            },
			error: function(){
				console.log('Error');
			}
		});
    }
    
    function loadCategory(categoryId,e){
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
					$(".cat-list-"+nextLevel+" .inner").html("");
					hasChild = 0;
				}
				
				if(aData[0] > 0){
					if(hasChild){
						n = parseInt(aData[0]) + 1;
						for(i = n; i <= 4; i++){
							$(".cat-list-"+i+" .inner").html("");
						}
					}
					
					$(".cat-list-"+aData[0]+" .inner").html(aData[1]);
					
				}
				
				
			},
			error: function(){
				console.log('Error');
			}
		});
    }
    
    txtOpen = 0;
    function searchTxtDivOpen(){
    	txtOpen = 1;
    	$("#searchTxtDiv").css("display","block");
    	//$("#btn-inner").fadeIn("slow");
    	$("#searchTxtDiv")
    	.animate({ height: "300px" }, 100 )
        .animate({ width: "1000px" }, 200 );
    	
    	$("#searchTxtBox").animate({backgroundColor: "#444444"}, 300);
    }
    
    function searchTxtDivClose(){
    	txtOpen = 0;
    	//$("#btn-inner").fadeOut("fast");
    	$("#searchTxtBox").animate({backgroundColor: "#222222"}, 300);
    	$("#searchTxtDiv")
    	.animate({ width: "0px" }, 200 )
    	.animate({ height: "0px" }, 100 )
    	$("#searchTxtDiv").fadeOut("fast");
    }
    
    function searchBtnDivOpen(){
    	if(txtOpen == 1){
    		searchTxtDivClose();
    	}
    	$("#searchBtnDiv").css("display","block");
    	$("#btn-inner").fadeIn("slow");
    	$("#searchBtnDiv")
    	.animate({ height: "300px" }, 100 )
        .animate({ width: "1000px" }, 200 );
    	$(".searchBtnInner").animate({backgroundColor: "#444444"}, 300);
    	
    	$("#searchBtnGly").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
    }
    
    function searchBtnDivClose(){
    	$("#searchBtnGly").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
    	$("#btn-inner").fadeOut("fast");
    	$(".searchBtnInner").animate({backgroundColor: "#f86d18"}, 300);
    	$("#searchBtnDiv")
    	.animate({ width: "0px" }, 200 )
    	.animate({ height: "0px" }, 100 )
    	$("#searchBtnDiv").fadeOut("fast");
    }
    
    
    
});
