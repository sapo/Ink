                <li class="" id="task{$_object_task->getId()}">
                  <span class="task">
                    <span class="left_options">
                      <span class="option checkbox">
                        {if $_object_task->canChangeCompleteStatus($logged_user)}
                          {link href=$_object_task->getOpenUrl(true) class=open_task}<img src='{image_url name=icons/checked.gif}' alt='' />{/link}
                        {else}
                          <img src="{image_url name=icons/checked.gif}" alt="toggle" />
                        {/if}
                      </span>
                    </span>
                    <span class="right_options">
                        {if module_loaded('timetracking') && $logged_user->getProjectPermission('timerecord', $_object_task->getProject())}
                        <span class="option">{object_time object=$_object_task show_time=no}</span>
                        {/if}
                        {if $_object_task->canDelete($logged_user)}
                        <span class="option">{link href=$_object_task->getTrashUrl() title='Move to Trash' class=remove_task}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}</span>
                        {/if}

                    </span>
                    <span class="main_data">
                      {$_object_task->getBody()|clean|clickable}
                      (<span class="details">{action_on_by action=Completed user=$_object_task->getCompletedBy() datetime=$_object_task->getCompletedOn()}</span>)
                    </span>
                  </span>
                </li>