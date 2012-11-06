    <div class="ink-section">
        <div class="ink-row ink-vspace">
            <div class="ink-l40">
                <div class="ink-gutter"> 
                    <h3 id="date_picker">Date Picker</h3>
                    <p>
                        As the name says, the <i>Date Picker</i> transforms a textbox into an element that, when in use, shows a calendar to help selecting a specific date.
                        It allows several <a href="#" class="modal">configurations</a>.
                    </p>
                </div>
            </div>
            <div class="ink-l60">
                <div class="ink-gutter">
					<form class="ink-form-block">
						<div class="ink-row">
							<div class="ink-form-wrapper ink-gutter ink-l20">
								<label for="dPicker" class="ink-form-inline ">Birthday</label>
								<input id="dPicker" type="text"></input>
							</div>
						</div>
					</form>
				</div>
			</div>
        </div>
    </div>
    <script type="text/javascript">
        var picker = new SAPO.Ink.DatePicker('#dPicker');
    </script>