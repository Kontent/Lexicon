/*
 * Lexicon JavaScript Tooltips Behavior.
 * 
 * @package		Lexicon
 * @subpackage	plgContentLexicon
 * @since		1.0
 */

// Extend Mootools Tips.
var Lexicon = Tips.extend({
	/*
	 * We are overriding the start method so that we can pass
	 * the data to our decode function to decode HTML entities.
	 */
	start: function(el){
		this.wrapper.empty();
		if (el.$tmp.myTitle){
			this.title = new Element('span').inject(new Element('div', {'class': this.options.className + '-title'}).inject(this.wrapper)).setHTML(this.decode(el.$tmp.myTitle));
		}
		if (el.$tmp.myText){
			this.text = new Element('span').inject(new Element('div', {'class': this.options.className + '-text'}).inject(this.wrapper)).setHTML(this.decode(el.$tmp.myText));
		}
		$clear(this.timer);
		this.timer = this.show.delay(this.options.showDelay, this);
	},
	
	/*
	 * Decode HTML entities.
	 */
	decode: function(str){
		str.replace(/&amp;/g, '&');
		str.replace(/&quot;/g, '"');
		str.replace(/&#039;/g, '\'');
		str.replace(/&lt;/g, '<');
		str.replace(/&gt;/g, '>');
		return str;
	}
});

/*
 * Create the lexicon behavior on domready.
 */
window.addEvent('domready', function(){
	var lex = new Lexicon($$('span.lexicon'), {
        maxTitleChars: 1024,
        className: 'lexicon'
    });
});