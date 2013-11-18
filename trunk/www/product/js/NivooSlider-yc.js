/*
---

script: NivooSlider.js

description: A nice image slider for MooTools.

license: MIT-style license

authors:
- Johannes Fischer

requires:
- core/1.4: '*'

provides:
- NivooSlider

...
 */
var NivooSlider=new Class({Implements:[Events,Options],caption:null,children:null,containerSize:0,count:0,currentSlide:0,currentImage:"",effects:{common:["fade"],horizontal:["foldDown","foldUp","sliceLeftUp","sliceLeftDown","sliceLeftRightDown","sliceLeftRightUp","sliceRightDown","sliceRightUp","wipeDown","wipeUp"],vertical:["foldLeft","foldRight","sliceDownLeft","sliceDownRight","sliceUpDownLeft","sliceUpDownRight","sliceUpLeft","sliceUpRight","wipeLeft","wipeRight"]},holder:null,hover:false,interval:null,isActive:true,orientation:"",paused:false,running:false,slices:null,sliceSize:null,totalSlides:0,options:{animSpeed:500,autoPlay:true,controlNav:true,controlNavItem:"disc",directionNav:true,directionNavHide:false,directionNavPosition:"inside",directionNavWidth:"20%",effect:"sliceDown",interval:3000,orientation:"vertical",pauseOnBlur:false,pauseOnHover:true,slices:15,preLoadImages:false},initialize:function(a,b){this.container=$(a);this.setOptions(b);this.orientation=this.getOrientation();this.effects.horizontal.combine(this.effects.common);this.effects.vertical.combine(this.effects.common);this.initSlider();this.createSlices();if(this.options.autoPlay){this.play();if(this.options.pauseOnBlur){window.addEvents({blur:function(){this.isActive=false;this.pause()}.bind(this),focus:function(){this.isActive=true;this.play()}.bind(this)})}}},animate:function(e,d,b){var a=e.retrieve("fxInstance"),c=b!==undefined&&b===true;a.start(d).chain(function(){this.count+=1;if(this.count===this.options.slices||c){this.running=false;this.finish();this.setBackgroundImage();this.count=0;if(this.currentSlide===(this.totalSlides-1)){this.lastSlide()}}}.bind(this))},arrangeSlices:function(){var b,a,d,c;this.slices.each(function(f,e){a={left:this.orientation==="vertical"?this.sliceSize.x*e:0,top:this.orientation==="horizontal"?this.sliceSize.y*e:0};if(this.orientation==="horizontal"){b=e===this.options.slices-1?this.containerSize.y-(this.sliceSize.y*e):this.sliceSize.y;c="100%";f.setStyles({height:b,top:a.top,width:c})}else{b=0;c=e===this.options.slices-1?this.containerSize.x-(this.sliceSize.x*e):this.sliceSize.x;f.setStyles({height:b,left:a.left,top:"",width:c})}f.store("fxInstance",new Fx.Morph(f,{duration:this.options.animSpeed})).store("coordinates",Object.merge(a,{height:b,width:c}))},this)},createCaption:function(){this.caption=new Element("p",{styles:{opacity:0}}).inject(this.holder);this.caption.store("fxInstance",new Fx.Morph(this.caption,{duration:200,wait:false}))},createControlNav:function(){var a="",c,b=1;this.container.addClass("got-control-nav");new Element("div.control-nav").inject(this.container);this.totalSlides.each(function(d){if(this.options.controlNavItem==="decimal"){c=b;b++}else{if(this.options.controlNavItem==="disc"){a="decimal";c="&bull;"}else{c=this.options.controlNavItem}}new Element("a",{"class":a,events:{click:function(f){f.stop();this.slide(d)}.bind(this)},href:"#",html:c}).inject(this.container.getElement("div.control-nav"))},this);this.setCurrentControlItem()},createDirectionNav:function(){var a,c,b,d;d=this.options.directionNavPosition==="inside"?this.holder:this.container;a={height:this.containerSize.y};if(this.options.directionNavPosition==="inside"&&this.options.directionNavWidth.toInt()!==0){a.width=this.options.directionNavWidth}c=new Element("div.direction-nav-left",{styles:a}).inject(d);b=new Element("div.direction-nav-right",{styles:a}).inject(d);this.leftNav=new Element("a",{events:{click:function(f){f.stop();if(this.options.autoPlay){this.pause();if(!this.options.pauseOnHover){this.play()}}this.previous()}.bind(this)},href:"#",styles:{height:a.height}}).inject(c);this.rightNav=new Element("a",{events:{click:function(f){f.stop();if(this.options.autoPlay){this.pause();if(!this.options.pauseOnHover){this.play()}}this.next()}.bind(this)},href:"#",styles:{height:a.height}}).inject(b);if(this.options.directionNavHide&&this.options.directionNav){$$(this.leftNav,this.rightNav).addClass("direction-nav-hide")}},createLinkHolder:function(){this.linkHolder=new Element("a.nivoo-link",{href:"#"}).inject(this.holder)},createSlices:function(){this.sliceSize={x:(this.containerSize.x/this.options.slices).round(),y:(this.containerSize.y/this.options.slices).round()};if(["fade","wipeLeft","wipeRight"].contains(this.options.effect)){this.options.slices=1}this.options.slices.each(function(a){new Element("div.nivoo-slice").inject(this.holder)},this);this.slices=this.getSlices();this.arrangeSlices()},getImages:function(){return this.holder.getElements("img")},getOrientation:function(){if(this.effects.horizontal.indexOf(this.options.effect)>-1){return"horizontal"}else{if(this.effects.vertical.indexOf(this.options.effect)>-1){return"vertical"}else{return this.options.orientation}}},getSlices:function(){return this.holder.getElements(".nivoo-slice")},initSlider:function(){if(this.options.directionNavPosition==="outside"){this.container.addClass("direction-nav-outside")}this.holder=new Element("div.nivoo-slider-holder").adopt(this.container.getChildren()).inject(this.container);this.containerSize=this.holder.getSize();this.children=this.getImages();this.totalSlides=this.children.length;this.children.setStyle("display","none");this.currentImage=this.children[0];this.createLinkHolder();this.setLink();this.setBackgroundImage();this.createCaption();this.showCaption();if(this.options.pauseOnHover&&this.options.autoPlay){this.holder.addEvents({mouseenter:function(){this.pause()}.bind(this),mouseleave:function(){this.play()}.bind(this)})}if(this.options.directionNav){this.createDirectionNav()}if(this.options.controlNav){this.createControlNav()}},hideCaption:function(){this.caption.retrieve("fxInstance").start({bottom:this.caption.getHeight()*-1,opacity:0.5})},next:function(){this.currentSlide+=1;if(this.currentSlide===this.totalSlides){this.currentSlide=0}this.slide()},pause:function(){window.clearInterval(this.interval);this.interval=null},play:function(){if(this.interval===null&&this.isActive===true){this.interval=this.next.periodical(this.options.interval,this)}},previous:function(){if(this.options.autoPlay){this.pause();if(!this.options.pauseOnHover){this.play()}}this.currentSlide-=1;if(this.currentSlide<0){this.currentSlide=(this.totalSlides-1)}this.slide()},setCurrentControlItem:function(){var a=this.container.getElement("div.control-nav a.current");if(a){a.removeClass("current")}this.container.getElements("div.control-nav a")[this.currentSlide].addClass("current")},showCaption:function(){var a=this.currentImage.get("title");if(!a){this.hideCaption();return}this.setCaptionText(a);this.caption.retrieve("fxInstance").start({bottom:0,opacity:1})},slide:function(b){var g,c,f,e,d,a;if(this.running){return}if(this.orientation==="random"){this.orientation=["horizontal","vertical"].getRandom()}this.arrangeSlices();if(b!==undefined){this.currentSlide=b}this.currentImage=this.children[this.currentSlide];if(this.options.controlNav){this.setCurrentControlItem()}this.setLink();this.showCaption();e=this.slices;a=0;this.slices.each(function(i){g=i.retrieve("coordinates");i.setStyles({background:'url("'+this.currentImage.get("src")+'") no-repeat -'+g.left+"px "+g.top*-1+"px",bottom:"",height:g.height,left:g.left,opacity:0,right:"",top:g.top,width:g.width});var h=this.orientation==="horizontal"?"width":"height";i.setStyle(h,0)},this);this.start();this.running=true;c=this.options.effect;if(c==="random"){c=this.effects[this.orientation].getRandom()}if(["sliceDownRight","sliceDownLeft"].contains(c)){if(c==="sliceDownLeft"){e=e.reverse()}e.each(function(h){h.setStyle("top",0);this.animate.delay(100+a,this,[h,{height:this.containerSize.y,opacity:1}]);a+=50},this)}else{if(["sliceUpRight","sliceUpLeft"].contains(c)){if(c==="sliceUpLeft"){e=e.reverse()}e.each(function(i){var h=i.retrieve("fxInstance");i.setStyle("bottom",0);this.animate.delay(100+a,this,[i,{height:this.containerSize.y,opacity:1}]);a+=50},this)}else{if(["sliceUpDownRight","sliceUpDownLeft"].contains(c)){if(c==="sliceUpDownLeft"){e=e.reverse()}e.each(function(j,h){if(h%2===0){j.setStyle("top",0)}else{j.setStyles({bottom:0,top:""})}this.animate.delay(100+a,this,[j,{height:this.containerSize.y,opacity:1}]);a+=50},this)}else{if(["wipeLeft","wipeRight"].contains(c)){d={height:this.containerSize.y,opacity:1,width:0};if(c==="wipeRight"){Object.append(d,{backgroundPosition:"top right",left:"",right:0})}f=e[0];f.setStyles(d);this.animate(f,{width:this.containerSize.x},true)}else{if(["sliceLeftUp","sliceLeftDown","sliceRightDown","sliceRightUp"].contains(c)){if(c==="sliceLeftUp"||c==="sliceRightUp"){e=e.reverse()}if(c==="sliceRightDown"||c==="sliceRightUp"){e.setStyles({left:"",right:0})}else{e.setStyles({left:0,right:""})}e.each(function(h){this.animate.delay(100+a,this,[h,{opacity:1,width:this.containerSize.x}]);a+=50},this)}else{if(["sliceLeftRightDown","sliceLeftRightUp"].contains(c)){if(c==="sliceLeftRightUp"){e=e.reverse()}e.each(function(j,h){if(h%2===0){j.setStyles({left:0,right:""})}else{j.setStyles({left:"",right:0})}this.animate.delay(100+a,this,[j,{opacity:1,width:this.containerSize.x}]);a+=50},this)}else{if(["wipeDown","wipeUp"].contains(c)){d={height:0,opacity:1,width:this.containerSize.x};if(c==="wipeUp"){Object.append(d,{backgroundPosition:"bottom left",bottom:0,top:""})}f=e[0];f.setStyles(d);this.animate(f,{height:this.containerSize.y},true)}else{if(["foldDown","foldLeft","foldRight","foldUp"].contains(c)){if(c==="foldUp"||c==="foldLeft"){e.reverse()}e.each(function(i){var h={opacity:1};if(this.orientation==="horizontal"){h.height=i.getHeight();i.setStyles({height:0,width:this.containerSize.x})}else{h.width=i.getWidth();i.setStyles({height:this.containerSize.y,width:0})}this.animate.delay(100+a,this,[i,h]);a+=50},this)}else{f=e[0];f.setStyles({height:this.containerSize.y,width:this.containerSize.x});this.animate(f,{opacity:1},true)}}}}}}}}},setBackgroundImage:function(){this.holder.setStyle("background-image",'url("'+this.currentImage.get("src")+'")')},setCaptionText:function(a){this.caption.set("text",a)},setLink:function(){var b,a=this.currentImage.getParent();if(a.get("tag")==="a"){b=a.clone(false).cloneEvents(a);b.replaces(this.linkHolder);this.linkHolder=b;this.linkHolder.addClass("nivoo-link").setStyle("display","block")}else{this.linkHolder.setStyle("display","none")}},finish:function(){this.fireEvent("finish")},lastSlide:function(){this.fireEvent("lastSlide")},start:function(){this.fireEvent("start")}});