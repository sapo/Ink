Ink.createModule('App.Tasks.DataProvider', '1', ['App.Tasks.DataProvider.Depot'], function(Depot) {

	var Module = function() {
		this.data = new Depot('db_1');
    };

    Module.prototype.listTasks = function() {
    	return this.data.all();
    };

    Module.prototype.getTask = function(id) {
    	return this.data.get(id);
    };
    
    Module.prototype.deleteTask = function(task) {
    	this.data.destroy(task);
    };
    
    Module.prototype.addTask = function(task) {
    	return this.data.save(task);
    };
    
    Module.prototype.updateTask = function(task) {
    	return this.data.update(task);
    };
    
    return new Module();
});
