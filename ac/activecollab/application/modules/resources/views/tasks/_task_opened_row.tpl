                <li class="{cycle values='odd,even'} sort"id="task{$_object_task->getId()}">
                  <span class="task">
                    <span class="left_options">
                      <span class="option star">{object_star object=$_object_task user=$logged_user}</span>
                      <span class="option checkbox">
                        {if $_object_task->canChangeCompleteStatus($logged_user)}
                          {link href=$_object_task->getCompleteUrl(true) class=complete_task}<img src="{image_url name=icons/not-checked.gif}" alt="toggle" />{/link}
                        {else}
                          <img src="{image_url name=icons/not-checked.gif}" alt="toggle" />
                        {/if}
                      </span>
                      <span class="option">{object_priority object=$_object_task}</span>
                    </span>
                    <span class="right_options">
                        <span class="option">{object_subscription object=$_object_task user=$logged_user}</span>
                      {if module_loaded('timetracking') && $logged_user->getProjectPermission('timerecord', $_object_task->getProject())}
                        <span class="option">{object_time object=$_object_task show_time=no}</span>
                      {/if}
                      {if $_object_task->canEdit($logged_user)}
                        <span class="option">{link href=$_object_task->getEditUrl() title='Edit...'}<img src='{image_url name=gray-edit.gif}' alt='' />{/link}</span>
                      {/if}
                      {if $_object_task->canDelete($logged_user)}
                        <span class="option">{link href=$_object_task->getTrashUrl() title='Move to Trash' class=remove_task}<img src='{image_url name=gray-delete.gif}' alt='' />{/link}</span>
                      {/if}
                    </span>
                    <span class="main_data">
                      <input type="hidden" name="task[{$_object_task->getId(true)}]" />
                      {$_object_task->getBody()|clean|clickable}
                    {if $_object_task->hasAssignees(true) && $_object_task->getDueOn()}
                      <span class="details block">{object_assignees object=$_object_task} | {due object=$_object_task}</span>
                    {elseif $_object_task->hasAssignees(true)}
                      <span class="details block">{object_assignees object=$_object_task}</span>
                    {elseif $_object_task->getDueOn()}
                      <span class="details block">{due object=$_object_task}</span>
                    {/if}
                    </span>
                  </span>
                </li>