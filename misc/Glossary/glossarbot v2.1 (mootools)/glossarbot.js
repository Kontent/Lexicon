
/* Turning on /off Glossary */

function gloss_swapClass(){ 
 var i,x,tB,j=0, tA=[], arg=gloss_swapClass.arguments;
 if(document.getElementsByTagName){for(i=4;i<arg.length;i++){tB=document.getElementsByTagName(arg[i]);
  for(x=0;x<tB.length;x++){tA[j]=tB[x];j++;}}for(i=0;i<tA.length;i++){
  if(tA[i].className){if(tA[i].id==arg[1]){if(arg[0]==1){
  tA[i].className=(tA[i].className==arg[3])?arg[2]:arg[3];}else{tA[i].className=arg[2];}
  }else if(arg[0]==1 && arg[1]=='none'){if(tA[i].className==arg[2] || tA[i].className==arg[3]){
  tA[i].className=(tA[i].className==arg[3])?arg[2]:arg[3];}
  }else if(tA[i].className==arg[2]){tA[i].className=arg[3];}}}}
}

function gloss_toggleImage(id) {
var tog = document.getElementById(id);
if(tog.style.display == 'none')
 { tog.style.display = '';}
else
{ tog.style.display = 'none';}
}

/* Tool tip settings */
/* Tips 1 */
var Tips1 = new Tips($$('.Tips1'));
�
/* Tips 2 */
var Tips2 = new Tips($$('.Tips2'), {
	initialize:function(){
		this.fx = new Fx.Style(this.toolTip, 'opacity', {duration: 500, wait: false}).set(0);
	},
	onShow: function(toolTip) {
		this.fx.start(1);
	},
	onHide: function(toolTip) {
		this.fx.start(0);
	}
});
�
/* Tips 3 */
var Tips3 = new Tips($$('.Tips3'), {
	showDelay: 400,
	hideDelay: 400,
	fixed: true
});
�
/* Tips 4 */
var Tips4 = new Tips($$('.Tips4'), {
	className: 'custom'
});

