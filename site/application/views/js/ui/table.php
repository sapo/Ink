    <div class="ink-section">
        <div class="ink-vspace">
            <div class="ink-l40">
                <div class="ink-space"> 
                    <h3 id="table">Table</h3>
                    <p>
                        The <i>Table</i> component provides an easy way to list data in a tabular format.
                        It supports sorting, pagination and getting data through <a href="#" class="modal">AJAX</a>.
                    </p>
                </div>
            </div>
            <div class="ink-l60">
                <div class="ink-gutter">
                    <div class="ink-l100">
                        <div class="ink-gutter">
                            <table></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var t = new SAPO.Ink.Table('table', {
            fields: ['name', 'age'],
            sortableFields: '*',
            model: [
                {name:'Jesus Christ',    age:33},
                {name:'Kurt Cobain',     age:27},
                {name:'Joni Mitchel',    age:27},
                {name:'Michael Jackson', age:51}
            ],
            pageSize: 2
        });
    </script>