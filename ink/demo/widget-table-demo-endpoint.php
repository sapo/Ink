<?php
    
    try {
        
        // DATA
        $rows = array(
            array(name => 'aa',  age => 32, gender => 'F', weight => 62),
            array(name => 'bb',  age => 3,  gender => 'M', weight => 23.223),
            array(name => 'acc', age => 13, gender => 'F', weight => 52.11),
            array(name => 'dd',  age => 10, gender => 'M', weight => 24.64),
            array(name => 'bee', age => 6,  gender => 'M', weight => 66.15),
            array(name => 'xc',  age => 32, gender => 'F', weight => 33.47),
            array(name => 'vd',  age => 50, gender => 'M'),
            array(name => 'zee', age => 53, gender => 'M', weight => 52.73),
        );
        
        
        // HANDLING
        $op = $_REQUEST['op'];
        
        if ($op == 'list') {
            $pageNr   = isset($_REQUEST['pageNr'])   ? intval( $_REQUEST['pageNr'])   : 0;
            $pageSz   = isset($_REQUEST['pageSz'])   ? intval( $_REQUEST['pageSz'])   : null;
            $orderBy  = isset($_REQUEST['orderBy'])  ?         $_REQUEST['orderBy']   : null;
            $orderDir = isset($_REQUEST['orderDir']) ? intval( $_REQUEST['orderDir']) : 1;
            
            // sorting
            if ($orderBy) {
                if (!$orderDir) {   $orderDir = 1;  }
                
                function cmp($a, $b) {
                global $orderBy, $orderDir;
                    if (gettype($a[$orderBy]) == 'string') {
                        return ($orderDir > 0) ? strcasecmp($a[$orderBy], $b[$orderBy]) : strcasecmp($b[$orderBy], $a[$orderBy]);
                    }
                    else {
                        $v = ($orderDir > 0) ? $a[$orderBy] - $b[$orderBy] : $b[$orderBy] - $a[$orderBy];
                        return $v / abs($v);
                    }
                }
                
                usort($rows, 'cmp');
            }
            
            
            // pagination
            if ($pageSz) {
                $page = array_slice($rows, $pageNr * $pageSz, $pageSz);
            }
            else {
                $page = $rows;
            }
            $params = array(
                pageNr=>$pageNr,
                pageSz=>$pageSz,
                orderBy=>$orderBy,
                orderDir=>$orderDir
            );
            
            header('Content-type: application/json');
            exit( json_encode( array(status=>'ok', items=>$page, params=>$params ) ) );
        }
        elseif ($op == 'count') {
            header('Content-type: application/json');
            exit( json_encode( array(status=>'ok', count=>count($rows)) ) );
        }
        elseif ($op == 'help') {
            // this operation is just to conveniently expose the API, remove it on your endpoint O:)
            header('Content-type: text/plain');
            ?>
Dummy example to illustrate the Table Component protocol:

This implementation uses a hardcoded array of data and performs sorting and pagination in PHP,
one might connect to a database or integrate with a service.
Passing the used params on response is not a requirement (it is not read by the table component), but helps debugging. 
    
    error response (on any op, message may differ):
        {"status":"error","message":"unsupported operation"}
    
    
    params for operation count:
        (none)
        
    count's successful response:
        {"status":"ok","count":5}
    
    
    params for operation list:
        pageNr:     0 - n-1
        pageSz:     1 - ...
        orderBy:    fieldName
        orderDir:   -1 / 1
    
    list's successful response:
        {   "status":"ok",
            "items":[
                {"name":"bee",  "age":3},
                {"name":"dd",   "age":3.3333333333333}
            ],
            "params":{
                "pageNr":   0,
                "pageSz":   2,
                "orderBy":  "age",
                "orderDir": 1
            }
        }
            
            <?php
        }
        else {
            throw new Exception('unsupported operation. use op=help to get details');
        }
    } catch (Exception $ex) {
        header('Content-type: application/json');
        exit( json_encode( array(status=>'error', message=>$ex->getMessage()) ) );
    }
