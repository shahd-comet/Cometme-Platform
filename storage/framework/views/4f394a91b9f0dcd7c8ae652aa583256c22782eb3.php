


<?php $__env->startSection('title', 'internet systems'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
 
<?php

    $systemsWithoutPrefix = [

        'Cables' => $cables, 
    ];

    $systemsWithPrefix = [

        'router' => $routers,
        'switch' => $switches,
        'controller' => $controllers,
        'ptp' => $ptps,
        'ap' => $aps,
        'ap_lite' => $apLites,
        'uisp' => $uisps,
        'electrician' => $electricians,
        'connector' => $connectors
    ]; 

    $totalSystemWithoutPrefix = 0;
    $totalSystemWithPrefix = 0;
    $grandTotalCost = 0;

    foreach ($systemsWithoutPrefix as $label => $systemInfo) {

        $totalSystemWithoutPrefix += $systemInfo->sum(function ($item) use ($label) {

            $cost = $item->cost ?? 0;
            $units = $item->unit ?? 0;
            return $cost * $units;
        });
    }

    foreach ($systemsWithPrefix as $label => $system) {

        $totalSystemWithPrefix += $system->sum(function ($item) use ($label) {

            $cost = $item->{$label . '_costs'} ?? 0;
            $units = $item->{$label . '_units'} ?? 0;
            return $cost * $units;
        });
    }

    $grandTotalCost = $totalSystemWithoutPrefix + $totalSystemWithPrefix;


    // Add cabinet + component costs
    foreach ($internetSystem->networkCabinets as $cabinet) {
        $cabinetCost = $cabinet->pivot->cost ?? 0;

        // Only sum components linked to this internet system
        $cabinetPivotSystem = $internetSystem->networkCabinetInternetSystems->firstWhere('id', $cabinet->pivot->id);
        $componentCost = $cabinetPivotSystem?->components->sum(fn($c) => $c->unit * $c->cost) ?? 0;

        $grandTotalCost += ($cabinetCost + $componentCost);
    }
?>



<?php $__currentLoopData = $systemsWithPrefix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $system): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $totalCost = $system->sum($label . '_costs');
    ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light"> <?php echo e($internetSystem->system_name); ?></span> Details
</h4>

<!-- <?php $__currentLoopData = $lineOfSightMainCommunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lineOfSightMainCommunity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="">
        <h4><?php echo e($lineOfSightMainCommunity->main_community_name); ?></h4>
        <img src="/assets/images/upload.gif" alt class="img-responsive"
        style=" transform: rotate(90deg)" width=90 height=90>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

 -->

<div class="container">
    <div class="card my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <h6>
                        System Name: 
                        <span class="spanDetails">
                            <?php echo e($internetSystem->system_name); ?>

                        </span>
                    </h6>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <h6>
                        System Types: 
                        <?php $__currentLoopData = $internetSystemTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $internetSystemType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="spanDetails">
                                <?php echo e($internetSystemType->InternetSystemType->name); ?>,
                            </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                    </h6>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <h6>
                        Community: 
                        <span class="spanDetails">
                            <?php echo e($internetSystem->Community->english_name ?? 'N/A'); ?>

                        </span>
                    </h6>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <h6>
                        Compound: 
                        <span class="spanDetails">
                            <?php echo e($internetSystem->Compound->english_name ?? 'N/A'); ?>

                        </span>
                    </h6>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="alert alert-success text-center">
                        <h5>Total System Cost: <strong><?php echo e(number_format($grandTotalCost, 2)); ?></strong> ₪</h5>
                    </div>
                </div>
            </div>

            <hr>
            <?php if(count($routers) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No Router Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                            Routers:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table id="internetSystemRouters" class="table table-info">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $routers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $router): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($router->model); ?></td>
                                    <td><?php echo e($router->brand_name); ?></td>
                                    <td><?php echo e($router->router_units); ?></td>
                                    <td><?php echo e($router->router_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($routers->sum('router_units')); ?></td>
                                    <td>
                                        <?php echo e($routers->sum(function ($router) {
                                                return ($router->router_costs ?? 0) * ($router->router_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
            
            <?php if(count($switches) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No Switches Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        Switches:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-warning">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $switches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $switch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($switch->model); ?></td>
                                    <td><?php echo e($switch->brand_name); ?></td>
                                    <td><?php echo e($switch->switch_units); ?></td>
                                    <td><?php echo e($switch->switch_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($switches->sum('switch_units')); ?></td>
                                    <td>
                                        <?php echo e($switches->sum(function ($switch) {
                                                return ($switch->switch_costs ?? 0) * ($switch->switch_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
   
            <?php if(count($controllers) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No Controllers Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        Controllers:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-primary">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $controllers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $controller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($controller->model); ?></td>
                                    <td><?php echo e($controller->brand); ?></td>
                                    <td><?php echo e($controller->controller_units); ?></td>
                                    <td><?php echo e($controller->controller_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($controllers->sum('controller_units')); ?></td>
                                    <td>
                                        <?php echo e($controllers->sum(function ($controller) {
                                                return ($controller->controller_costs ?? 0) * ($controller->controller_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>

            <?php if(count($aps) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No AP Meshes Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        Ap Meshes:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-success">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $aps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($ap->model); ?></td>
                                    <td><?php echo e($ap->brand); ?></td>
                                    <td><?php echo e($ap->ap_units); ?></td>
                                    <td><?php echo e($ap->ap_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($aps->sum('ap_units')); ?></td>
                                    <td>
                                        <?php echo e($aps->sum(function ($ap) {
                                                return ($ap->ap_costs ?? 0) * ($ap->ap_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
                

            <?php if(count($apLites) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No AP Lites Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        AP Lites:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-danger">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $apLites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $apLite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($apLite->model); ?></td>
                                    <td><?php echo e($apLite->brand); ?></td>
                                    <td><?php echo e($apLite->ap_lite_units); ?></td>
                                    <td><?php echo e($apLite->ap_lite_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($apLites->sum('ap_lite_units')); ?></td>
                                    <td>
                                        <?php echo e($apLites->sum(function ($ap) {
                                                return ($ap->ap_lite_costs ?? 0) * ($ap->ap_lite_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>

            <?php if(count($ptps) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No PTP Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        PTP:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-secondary">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $ptps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ptp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($ptp->model); ?></td>
                                    <td><?php echo e($ptp->brand); ?></td>
                                    <td><?php echo e($ptp->ptp_units); ?></td>
                                    <td><?php echo e($ptp->ptp_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($ptps->sum('ptp_units')); ?></td>
                                    <td>
                                        <?php echo e($ptps->sum(function ($ptp) {
                                                return ($ptp->ptp_costs ?? 0) * ($ptp->ptp_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
             
            <?php if(count($uisps) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No UISP Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        UISP:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-dark">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $uisps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uisp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($uisp->model); ?></td>
                                    <td><?php echo e($uisp->brand); ?></td>
                                    <td><?php echo e($uisp->uisp_units); ?></td>
                                    <td><?php echo e($uisp->uisp_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($uisps->sum('uisp_units')); ?></td>
                                    <td>
                                        <?php echo e($uisps->sum(function ($uisp) {
                                                return ($uisp->uisp_costs ?? 0) * ($uisp->uisp_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
             
            <?php if(count($electricians) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No Electrician Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        Electricians:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-warning">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $electricians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $electrician): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($electrician->model); ?></td>
                                    <td><?php echo e($electrician->brand); ?></td>
                                    <td><?php echo e($electrician->electrician_units); ?></td>
                                    <td><?php echo e($electrician->electrician_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($electricians->sum('electrician_units')); ?></td>
                                    <td>
                                        <?php echo e($electricians->sum(function ($electrician) {
                                                return ($electrician->electrician_costs ?? 0) * ($electrician->electrician_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>
             

            <?php if(count($connectors) < 1): ?>
                <div class="alert alert-warning">
                    <strong>Sorry!</strong> No Connector Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        Connector:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-primary">
                            <thead>
                                <tr>
                                    <th >Model</th>
                                    <th >Brand</th>
                                    <th >Units</th>
                                    <th >Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $connectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($connector->model); ?></td>
                                    <td><?php echo e($connector->brand); ?></td>
                                    <td><?php echo e($connector->connector_units); ?></td>
                                    <td><?php echo e($connector->connector_costs); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan=2>Total</td>
                                    <td><?php echo e($connectors->sum('connector_units')); ?></td>
                                    <td>
                                        <?php echo e($connectors->sum(function ($connector) {
                                                return ($connector->connector_costs ?? 0) * ($connector->connector_units ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>


            <?php if(count($cables) < 1): ?>
                <div class="alert alert-warning">
                    No Cables Found.
                </div>                                      
            <?php else: ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <h6>
                        Cables:
                        </h6>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12" >
                        <table  class="table table-primary">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th >Units</th>
                                    <th>Cost per unit</th>
                                </tr>
                            </thead>
                            <?php $__currentLoopData = $cables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cable): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th></th>
                                    <td><?php echo e($cable->unit); ?></td>
                                    <td><?php echo e($cable->cost); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                            <tfoot>
                                <tr class="table-light">
                                    <td >Total Units</td>
                                    <td><?php echo e($cables->sum('unit')); ?></td>
                                    <td>
                                        <?php echo e($cables->sum(function ($cable) {
                                                return ($cable->cost ?? 0) * ($cable->unit ?? 0);
                                            })); ?>

                                    </td>
                                </tr>
                            </tfoot>
                         </table>
                    </div>
                </div>
                <hr>
            <?php endif; ?>

            

        <?php if($internetSystem->networkCabinets->isEmpty()): ?>
            <div class="alert alert-warning text-center">
                <strong>Sorry!</strong> No Network Cabinets Found.
            </div>
        <?php else: ?> 
            <div class="row">
                <div class="col-12 mb-3">
                    <h5><i class="fa fa-server me-1"></i> Network Cabinets</h5>
                </div>
            </div>

            <?php $__currentLoopData = $internetSystem->networkCabinets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cabinet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <strong><?php echo e($cabinet->model); ?></strong>
                        <span class="badge bg-light text-dark">Cabinet Cost: <?php echo e(number_format($cabinet->pivot->cost ?? 0, 2)); ?> ₪</span>
                    </div>

                    <div class="card-body">
                        <?php
                            $cabinetPivotSystem = $internetSystem->networkCabinetInternetSystems->firstWhere('id', $cabinet->pivot->id);
                            $components = $cabinetPivotSystem?->components ?? collect();
                            $componentTotal = $components->sum(fn($c) => $c->unit * $c->cost);
                            $grandCabinetTotal = ($cabinet->pivot->cost ?? 0) + $componentTotal;
                            $grouped = $components->groupBy('component_type');
                        ?>

                        <p>
                            <strong>Total Cost for Cabinet & its components:</strong> 
                            <span class="badge bg-success"><?php echo e(number_format($grandCabinetTotal, 2)); ?> ₪</span>
                        </p>

                        <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $components): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-4">
                                <h6 class="text-muted"><?php echo e(class_basename($type)); ?>s</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Model</th>
                                                <th>Units</th>
                                                <th>Cost</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $components; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($component->component->model ?? '—'); ?></td>
                                                    <td><?php echo e($component->unit); ?></td>
                                                    <td><?php echo e(number_format($component->cost, 2)); ?> ₪</td>
                                                    <td class="fw-bold">
                                                        <?php echo e(number_format($component->unit * $component->cost, 2)); ?> ₪
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/system/internet/show.blade.php ENDPATH**/ ?>