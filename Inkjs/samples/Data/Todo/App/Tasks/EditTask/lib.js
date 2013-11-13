Ink.createModule('App.Tasks.EditTask', '1', ['Ink.Data.Binding_1', 'App.Tasks.DataProvider', 'App.Tasks'], function(ko, dataProvider, app) {
    var Module = function() {
    	var self=this;
    	
        this.moduleName = 'App.Tasks.EditTask';
        this.subject = ko.observable('');
        this.description = ko.observable('');
        this.phoneNumber = ko.observable('');
        this.date = ko.observable('');
        this.status = ko.observable('');
        this.task = undefined;
        
        this.invalidSubject = ko.computed(function() {
        	return self.subject().trim().length == 0;
        });
    };

    Module.prototype.initialize = function(data) {
    	if (!data.id) {
    		this.task=undefined;

    		this.subject('');
    		this.description('');
    		this.phoneNumber('');
    		this.date('');
    		this.status('todo');
    	} else {
    		this.task = dataProvider.getTask(data.id);

    		this.subject(this.task.subject);
    		this.description(this.task.description);
    		this.phoneNumber(this.task.phoneNumber);
    		this.date(this.task.date);
    		this.status(this.task.status);
    	}
    };
    
    Module.prototype.saveTask = function() {
    	if (this.invalidSubject()) {
    		return;
    	}
    	
    	if (!this.task) { // New Task
    		this.task = {
    			subject: this.subject(),
    			description: this.description(),
    			phoneNumber: this.phoneNumber(),
    			date: this.date(),
    			status: this.status()
    		};
    		
    		dataProvider.addTask(this.task);
    		
    		app.signals.taskAdded.dispatch(this.task);
    	} else {
    		this.task.subject = this.subject();
    		this.task.description = this.description();
    		this.task.phoneNumber = this.phoneNumber();
    		this.task.date = this.date();
    		this.task.status = this.status();
    		
    		dataProvider.updateTask(this.task);
    		
    		app.signals.taskUpdated.dispatch(this.task);
    	}
    	
    	app.navigateTo(this.task.status);
    };
    
    return new Module();
});
