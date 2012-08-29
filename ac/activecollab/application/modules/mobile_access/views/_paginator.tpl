{if $_mobile_access_paginator->getLastPage() > 1}
  <div class="paginator">
    {if !$_mobile_access_paginator->isFirst()}
      <a href="{$_mobile_access_paginator_prev_url}" class="paginator_button previous"><span>{lang}Previous{/lang}</span></a>
    {/if}
    <span class="paginator_status">
      {lang current=$_mobile_access_paginator->getCurrentPage() last=$_mobile_access_paginator->getLastPage()}Page :current of :last{/lang}
    </span>
    {if !$_mobile_access_paginator->isLast()}
      <a href="{$_mobile_access_paginator_next_url}" class="paginator_button next"><span>{lang}Next{/lang}</span></a>
    {/if}
  </div>
{/if}