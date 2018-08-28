!function(t){"use strict";var o=t.fn.bootstrapTable.utils.sprintf;t.extend(t.fn.bootstrapTable.defaults,{stickyHeader:!1});var e=3;try{e=parseInt(t.fn.dropdown.Constructor.VERSION,10)}catch(t){}var a=e>3?"d-none":"hidden",i=t.fn.bootstrapTable.Constructor,s=i.prototype.initHeader;i.prototype.initHeader=function(){function e(o){var e=o.data,s=e.find("thead").attr("id");if(e.length<1||t("#"+r).length<1)return t(window).off("resize."+r),t(window).off("scroll."+r),void e.closest(".fixed-table-container").find(".fixed-table-body").off("scroll."+r);var d="0";n.options.stickyHeaderOffsetY&&(d=n.options.stickyHeaderOffsetY.replace("px",""));var l=t(window).scrollTop(),f=t("#"+p).offset().top-d,u=t("#"+h).offset().top-d-t("#"+s).css("height").replace("px","");if(l>f&&l<=u){t.each(n.$stickyHeader.find("tr").eq(0).find("th"),function(o,e){t(e).css("min-width",t("#"+s+" tr").eq(0).find("th").eq(o).css("width"))}),t("#"+c).removeClass(a).addClass("fix-sticky fixed-table-container"),t("#"+c).css("top",d+"px");var b=t('<div style="position:absolute;width:100%;overflow-x:hidden;" />');t("#"+c).html(b.append(n.$stickyHeader)),i(o)}else t("#"+c).removeClass("fix-sticky").addClass(a)}function i(o){var e=o.data,a=e.find("thead").attr("id");t("#"+c).css("width",+e.closest(".fixed-table-body").css("width").replace("px","")+1),t("#"+c+" thead").parent().scrollLeft(Math.abs(t("#"+a).position().left))}var n=this;if(s.apply(this,Array.prototype.slice.apply(arguments)),this.options.stickyHeader){var d=this.$tableBody.find("table"),r=d.attr("id"),l=d.attr("id")+"-sticky-header",c=l+"-sticky-header-container",p=l+"_sticky_anchor_begin",h=l+"_sticky_anchor_end";d.before(o('<div id="%s" class="%s"></div>',c,a)),d.before(o('<div id="%s"></div>',p)),d.after(o('<div id="%s"></div>',h)),d.find("thead").attr("id",l),this.$stickyHeader=t(t("#"+l).clone(!0,!0)),this.$stickyHeader.removeAttr("id"),t(window).on("resize."+r,d,e),t(window).on("scroll."+r,d,e),d.closest(".fixed-table-container").find(".fixed-table-body").on("scroll."+r,d,i),this.$el.on("all.bs.table",function(o){n.$stickyHeader=t(t("#"+l).clone(!0,!0)),n.$stickyHeader.removeAttr("id")})}}}(jQuery),function(t){"use strict";var o=!1,e=t.fn.bootstrapTable.utils.sprintf,a=function(o,a,s,n){if(t("#avdSearchModal_"+n.options.idTable).hasClass("modal"))t("#avdSearchModal_"+n.options.idTable).modal();else{var d=e('<div id="avdSearchModal%s"  class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">',"_"+n.options.idTable);d+='<div class="modal-dialog modal-xs">',d+=' <div class="modal-content">',d+='  <div class="modal-header">',d+='   <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >&times;</button>',d+=e('   <h4 class="modal-title">%s</h4>',a),d+="  </div>",d+='  <div class="modal-body modal-body-custom">',d+=e('   <div class="container-fluid" id="avdSearchModalContent%s" style="padding-right: 0px;padding-left: 0px;" >',"_"+n.options.idTable),d+="   </div>",d+="  </div>",d+="  </div>",d+=" </div>",d+="</div>",t("body").append(t(d));var r=i(o,s,n),l=0;t("#avdSearchModalContent_"+n.options.idTable).append(r.join("")),t("#"+n.options.idForm).off("keyup blur","input").on("keyup blur","input",function(t){clearTimeout(l),l=setTimeout(function(){n.onColumnAdvancedSearch(t)},n.options.searchTimeOut)}),t("#btnCloseAvd_"+n.options.idTable).click(function(){t("#avdSearchModal_"+n.options.idTable).modal("hide")}),t("#avdSearchModal_"+n.options.idTable).modal()}},i=function(t,o,a){var i=[];i.push(e('<form class="form-horizontal" id="%s" action="%s" >',a.options.idForm,a.options.actionForm));for(var s in t){var n=t[s];!n.checkbox&&n.visible&&n.searchable&&(i.push('<div class="form-group">'),i.push(e('<label class="col-sm-4 control-label">%s</label>',n.title)),i.push('<div class="col-sm-6">'),i.push(e('<input type="text" class="form-control input-md" name="%s" placeholder="%s" id="%s">',n.field,n.title,n.field)),i.push("</div>"),i.push("</div>"))}return i.push('<div class="form-group">'),i.push('<div class="col-sm-offset-9 col-sm-3">'),i.push(e('<button type="button" id="btnCloseAvd%s" class="btn btn-default" >%s</button>',"_"+a.options.idTable,o)),i.push("</div>"),i.push("</div>"),i.push("</form>"),i};t.extend(t.fn.bootstrapTable.defaults,{advancedSearch:!1,idForm:"advancedSearch",actionForm:"",idTable:void 0,onColumnAdvancedSearch:function(t,o){return!1}}),t.extend(t.fn.bootstrapTable.defaults.icons,{advancedSearchIcon:"glyphicon-chevron-down"}),t.extend(t.fn.bootstrapTable.Constructor.EVENTS,{"column-advanced-search.bs.table":"onColumnAdvancedSearch"}),t.extend(t.fn.bootstrapTable.locales,{formatAdvancedSearch:function(){return"Advanced search"},formatAdvancedCloseButton:function(){return"Close"}}),t.extend(t.fn.bootstrapTable.defaults,t.fn.bootstrapTable.locales);var s=t.fn.bootstrapTable.Constructor,n=s.prototype.initToolbar,d=s.prototype.load,r=s.prototype.initSearch;s.prototype.initToolbar=function(){if(n.apply(this,Array.prototype.slice.apply(arguments)),this.options.search&&this.options.advancedSearch&&this.options.idTable){var t=this,o=[];o.push(e('<div class="columns columns-%s btn-group pull-%s" role="group">',this.options.buttonsAlign,this.options.buttonsAlign)),o.push(e('<button class="btn btn-default%s" type="button" name="advancedSearch" aria-label="advanced search" title="%s">',void 0===t.options.iconSize?"":" btn-"+t.options.iconSize,t.options.formatAdvancedSearch())),o.push(e('<i class="%s %s"></i>',t.options.iconsPrefix,t.options.icons.advancedSearchIcon)),o.push("</button></div>"),t.$toolbar.prepend(o.join("")),t.$toolbar.find('button[name="advancedSearch"]').off("click").on("click",function(){a(t.columns,t.options.formatAdvancedSearch(),t.options.formatAdvancedCloseButton(),t)})}},s.prototype.load=function(e){if(d.apply(this,Array.prototype.slice.apply(arguments)),this.options.advancedSearch&&void 0!==this.options.idTable&&!o){var a=parseInt(t(".bootstrap-table").height());a+=10,t("#"+this.options.idTable).bootstrapTable("resetView",{height:a}),o=!0}},s.prototype.initSearch=function(){if(r.apply(this,Array.prototype.slice.apply(arguments)),this.options.advancedSearch){var o=this,e=t.isEmptyObject(this.filterColumnsPartial)?null:this.filterColumnsPartial;this.data=e?t.grep(this.data,function(a,i){for(var s in e){var n=e[s].toLowerCase(),d=a[s];if(d=t.fn.bootstrapTable.utils.calculateObjectValue(o.header,o.header.formatters[t.inArray(s,o.header.fields)],[d,a,i],d),-1===t.inArray(s,o.header.fields)||"string"!=typeof d&&"number"!=typeof d||-1===(d+"").toLowerCase().indexOf(n))return!1}return!0}):this.data}},s.prototype.onColumnAdvancedSearch=function(o){var e=t.trim(t(o.currentTarget).val()),a=t(o.currentTarget)[0].id;t.isEmptyObject(this.filterColumnsPartial)&&(this.filterColumnsPartial={}),e?this.filterColumnsPartial[a]=e:delete this.filterColumnsPartial[a],this.options.pageNumber=1,this.onSearch(o),this.updatePagination(),this.trigger("column-advanced-search",a,e)}}(jQuery);