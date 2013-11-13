Ink.createModule('App.Tasks.Home', '1', ['App.Tasks', 'Ink.Data.Binding_1', 'App.Tasks.DataProvider'], function(app, ko, dataProvider) {
    var Module = function() {
        this.moduleName = 'App.Tasks.Home';
        this.tasks = ko.observableArray();
        this.tasksModel = new ko.simpleGrid.viewModel({
            data: this.tasks,
            pageSize: 10000,
            columns: [
              {headerText: ko.observable(''), rowTemplate: 'taskItemTemplate'},
            ]
        });
        this.tasksModel.parentModel = this;
        
        this.loadTasks();
        
        app.signals.taskAdded.add(this.taskAddedHandler.bind(this));
        app.signals.taskUpdated.add(this.taskUpdatedHandler.bind(this));
    };

    Module.prototype.initialize = function(data) {
    	if (!data.filter || data.filter == 'todo') {
    		this.tasksModel.columns[0].headerText('To-do');
    		this.tasks(this.todoTasks);
    	} else {
    		if (data.filter == 'complete') {
        		this.tasksModel.columns[0].headerText('Completed');
        		this.tasks(this.completedTasks);
    		} else {
        		this.tasksModel.columns[0].headerText('Incomplete');
        		this.tasks(this.incompleteTasks);
    		}
    	}
    };

    Module.prototype.loadTasks = function() {
    	var tasks = dataProvider.listTasks();
    	var task;

        this.todoTasks = [];
        this.completedTasks = [];        
        this.incompleteTasks = [];
    	
    	for (var i=0; i<tasks.length; i++) {
    		task = tasks[i];
    		
    		if (task.status=='todo') {
    			this.todoTasks.push(task);
    		}
    		
    		if (task.status=='completed') {
    			this.completedTasks.push(task);
    		}
    		
    		if (task.status=='incomplete') {
    			this.incompleteTasks.push(task);
    		}
    	}
    };
    
    Module.prototype.afterRender = function() {
        document.getElementById('mainMenuDropDown').style.display = 'none';  
    };
    
    Module.prototype.taskAddedHandler = function(task) {
    	this.loadTasks();
    };
    
    Module.prototype.taskUpdatedHandler = function(task) {
    	this.loadTasks();
    };
    
    Module.prototype.editTask = function(task) {
    	app.navigateTo('edit?id='+task._id);
    };

    return new Module();
});
