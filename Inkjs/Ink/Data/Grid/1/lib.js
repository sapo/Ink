/**
 * @module Ink.Data.Grid
 * @desc Data grid widget
 * @author hlima, ecunha, ttt  AT sapo.pt
 * @version 1
 */
Ink.createModule('Ink.Data.Grid', '1', ['Ink.Data.Module_1'], function(ko) {
	/*
	 *  knockout.simpleGrid1.3.js | (c) 2013 Ryan Niemeyer |  http://www.opensource.org/licenses/mit-license
	 * 
	 *  With changes from SAPO's Intra team (eg. better pagination, sort header icon)
 	 */
	(function () {
	    // Private function
	    function getColumnsForScaffolding(data) {
	        if ((typeof data.length !== 'number') || data.length === 0) {
	            return [];
	        }
	        var columns = [];
	        for (var propertyName in data[0]) {
	            columns.push({ headerText: propertyName, rowText: propertyName });
	        }
	        return columns;
	    }

	    ko.simpleGrid = {
	        // Defines a view model class you can use to populate a grid
	        viewModel: function (configuration) {
	            var sortHandler;
	            var self=this;
	            
	            this.data = configuration.data;
	            this.currentPageIndex = ko.observable(0);
	            this.pageSize = configuration.pageSize || 5;
	            this.sortedColumn = undefined;
	            
	            // If the backing data's record count changes, let's navigate to the first page
	            if (ko.isObservable(this.data)) {
	                this.data.subscribe(function() {
	                   self.currentPageIndex(0); 
	                });
	            }

	            // If you don't specify columns configuration, we'll use scaffolding
	            this.columns = configuration.columns || getColumnsForScaffolding(ko.utils.unwrapObservable(this.data));
	            
	            // INTRA: Wrap the sort handler to introduce the header icon update logic
	            for (var columnIndex=0; columnIndex<this.columns.length; columnIndex++) {
	                sortHandler = this.columns[columnIndex].headerSortHandler;
	                
	                if (typeof sortHandler == 'function') {
	                    this.columns[columnIndex].headerSortHandler = (function() {
	                        var wrappedHandler = sortHandler;
	                        
	                        return function(column) {
	                            if (self.sortedColumn && (self.sortedColumn != column) ) {
	                                self.sortedColumn.headerSortOrder('sort');
	                            }
                                    self.sortedColumn = column;

	                            if (column.headerSortOrder() == 'sort')
	                                column.headerSortOrder('asc');
	                            else if (column.headerSortOrder() == 'asc')
	                                column.headerSortOrder('desc');
	                            else
	                                column.headerSortOrder('sort');
	                            
	                            wrappedHandler(column);
	                        } 
	                    })();
	                }
	            }

	            this.itemsOnCurrentPage = ko.computed(function () {
	                var startIndex = this.pageSize * this.currentPageIndex();
	                return this.data.slice(startIndex, startIndex + this.pageSize);
	            }, this);

	            this.maxPageIndex = ko.computed(function () {
	                return Math.ceil(ko.utils.unwrapObservable(this.data).length / this.pageSize) - 1;
	            }, this);
	            

	            this._buildPages = function(pages, start, end) {
	                var page;

	                for (var i=start;i<=end;i++) {
	                    page = {};
	                    page['pageNum'] = i+1;
	                    page['active'] = (i==self.currentPageIndex());
	                    page['dots'] = false;
	                
	                    // vamos criar uma closure para ficarmos com uma cópia do numero da página para o click handler
	                    (function() {
	                        var pageNum=i;
	                        
	                        page['goTo'] = function() {
	                            self.currentPageIndex(pageNum);
	                        };
	                    })();
	                
	                    pages.push(page);
	                }
	            };
    
    	            this.pages = ko.computed(function() {
    	                var pages = [];
    	                var page;
    	                var start = self.currentPageIndex()-5;
    	                var end = self.currentPageIndex()+5;
    
    	                // Caso extremo 1
    	                if (self.maxPageIndex()<13) {
    	                    self._buildPages(pages, 0, self.maxPageIndex());
    	                    return pages;
    	                }
    	        
    	                // Caso extremo 2
    	                if (self.currentPageIndex()<=6) {
    	                    self._buildPages(pages, 0, 11);
	                    if (self.maxPageIndex()>=10) {
	                        page = {};
	                        page['pageNum'] = '...';
	                        page['active'] = false;
	                        page['dots'] = true;
	                        pages.push(page);

	                        page = {};
	                        page['pageNum'] = self.maxPageIndex()+1;
	                        page['active'] = (self.maxPageIndex()==self.currentPageIndex());
	                        page['dots'] = false;
	                        page['goTo'] = function() {
	                            self.currentPageIndex(self.maxPageIndex());
	                        };

	                        pages.push(page);
	                    }
	                    return pages;
    	                }
    	        
    	                // Caso extremo 3
    	                if (self.currentPageIndex()>=self.maxPageIndex()-6) {
    	                    page = {};
	                    page['pageNum'] = 1;
	                    page['active'] = (0==self.currentPageIndex());
	                    page['dots'] = false;
	                    page['goTo'] = function() {
	                        self.currentPageIndex(0);
	                    };
	                    pages.push(page);
	                
	                    page = {};
	                    page['pageNum'] = '...';
	                    page['active'] = false;
	                    page['dots'] = true;
	                    pages.push(page);

	                    self._buildPages(pages, self.maxPageIndex()-11, self.maxPageIndex());
	                
	                    return pages;
    	                }
    	        
    	                // Todos os outros casos
    	                page = {};
    	                page['pageNum'] = 1;
    	                page['active'] = (0==self.currentPageIndex());
    	                page['dots'] = false;
    	                page['goTo'] = function() {
    	                    self.currentPageIndex(0);
    	                };
    	                pages.push(page);
    	        
    	                page = {};
    	                page['pageNum'] = '...';
    	                page['active'] = false;
    	                page['dots'] = true;
    	                pages.push(page);
    	        
    	                self._buildPages(pages, start, end);
    	        
    	                page = {};
    	                page['pageNum'] = '...';
    	                page['active'] = false;
    	                page['dots'] = true;
    	                pages.push(page);
    
    	                page = {};
    	                page['pageNum'] = self.maxPageIndex()+1;
    	                page['active'] = (self.maxPageIndex()==self.currentPageIndex());
    	                page['dots'] = false;
    	                page['goTo'] = function() {
    	                    self.currentPageIndex(self.maxPageIndex());
    	                };
    	                pages.push(page);
    	        
    	                return pages;
    	            });
    	                
    	            this.backDisabled = ko.computed(function() {
    	                return self.currentPageIndex()==0;
    	            }); 
    
    	            this.forwardDisabled = ko.computed(function() {
    	                return self.currentPageIndex()==self.maxPageIndex();
    	            });

    	            this.stepForward = function() {
    	                if (self.currentPageIndex()<this.maxPageIndex())
                            this.currentPageIndex(this.currentPageIndex()+1);
    	            };
             
    	            this.stepBackward = function() {
    	                if (this.currentPageIndex()>0)
                            this.currentPageIndex(this.currentPageIndex()-1);
    	            };
    	            
	        }
	    };

	    // The "simpleGrid" binding
	    ko.bindingHandlers.simpleGrid = {
	        init: function() {
	            return { 'controlsDescendantBindings': true };
	        },
	        // This method is called to initialize the node, and will also be called again if you change what the grid is bound to
	        update: function (element, viewModelAccessor, allBindingsAccessor) {
	            var viewModel = viewModelAccessor(), allBindings = allBindingsAccessor();

	            // Empty the element
	            while(element.firstChild)
	                ko.removeNode(element.firstChild);

	            // Allow the default templates to be overridden
	            var gridTemplateName  = allBindings.simpleGridTemplate || "Ink.Data.Grid.InkGridTemplate",
                        pageLinksTemplateName = allBindings.simpleGridPagerTemplate || "Ink.Data.Grid.InkPagerTemplate";

	            // Render the main grid
	            var gridContainer = element.appendChild(document.createElement("DIV"));
	            ko.renderTemplate(gridTemplateName, viewModel, {}, gridContainer, "replaceNode");

	            // Render the page links
	            var pageLinksContainer = element.appendChild(document.createElement("DIV"));
	            ko.renderTemplate(pageLinksTemplateName, viewModel, {}, pageLinksContainer, "replaceNode");
	        }
	    };
	})();
	
	return {};
});