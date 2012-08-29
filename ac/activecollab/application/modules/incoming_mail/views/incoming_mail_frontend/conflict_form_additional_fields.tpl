        {wrap field=created_by_id}
          {label for=created_by_id}Select new owner for this object:{/label}
          {incoming_mail_select_user name=mail[created_by_id] project=$project id='user_id' value=$form_data.user_id}
        {/wrap}
        
        {wrap field=object_type}
          {label for=object_type}Object Type:{/label}
          {select_incoming_mail_object_type name=mail[object_type] skip_comment=false id='object_type' value=$form_data.object_type}
        {/wrap}
        
        {wrap field=parent_id id='parent_id_block'}
          {label for=object_type}Parent Object:{/label}
          {select_project_object name=mail[parent_id] project=$project id='parent_id'}
        {/wrap}