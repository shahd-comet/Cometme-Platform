<!-- Action Items for adding english name for the internet contract holders -->
<li class="timeline-item mb-md-4 mb-5 timeline-item-left">
    <span class="timeline-indicator timeline-indicator-warning" data-aos="zoom-in" data-aos-delay="200">
        <i class="bx bx-bulb"></i>
    </span>
    <div class="timeline-event card p-0" data-aos="fade-left">
        <h6 class="card-header"></h6>
        <div class="card-body">
            <div class="mb-4">
                <h6>Installation Year</h6>
                <?php if(count($missingEnergySystemInstallationYear) > 0): ?>
                    <p>Add the 
                        <span> missing Installation year </span> 
                        for these energy systems: 
                    </p>
                    <?php $__currentLoopData = $missingEnergySystemInstallationYear; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingEnergySystemYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-unstyled">
                        <li class="d-flex" style="margin-top:9px">
                            <a class="btn btn-warning btn-sm" type="button" 
                                href="/energy-system/<?php echo e($missingEnergySystemYear->id); ?>/edit" target="_blank">
                                <span> <?php echo e($missingEnergySystemYear->name); ?> </span>   
                            </a>
                        </li>
                    </ul>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <span>All is well!</span>
                <?php endif; ?>
            </div>

            <hr>
            <div class="mb-4">
                <h6>Cycle Year</h6>
                <?php if(count($missingEnergySystemCycleYear) > 0): ?>
                <p>Add the 
                    <span> missing cycle year </span> 
                    for these energy systems: 
                </p>
                <?php $__currentLoopData = $missingEnergySystemCycleYear; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingEnergySystemCycle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul class="list-unstyled">
                    <li class="d-flex" style="margin-top:9px">
                        <a class="btn btn-warning btn-sm" type="button" 
                            href="/energy-system/<?php echo e($missingEnergySystemCycle->id); ?>/edit" target="_blank">
                            <span> <?php echo e($missingEnergySystemCycle->name); ?> </span>   
                        </a>
                    </li>
                </ul>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <span>All is well!</span>
                <?php endif; ?>
            </div>

            <hr>
            <div class="mb-4">
                <h6>Rated Power</h6>
                <?php if(count($missingEnergySystemRatedPower) > 0): ?>
                <p>Add the 
                    <span> missing rated power </span> 
                    for these energy systems: 
                </p>
                <?php $__currentLoopData = $missingEnergySystemRatedPower; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingEnergySystemRated): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul class="list-unstyled">
                    <li class="d-flex" style="margin-top:9px">
                        <a class="btn btn-warning btn-sm" type="button" 
                            href="/energy-system/<?php echo e($missingEnergySystemRated->id); ?>/edit" target="_blank">
                            <span> <?php echo e($missingEnergySystemRated->name); ?> </span>   
                        </a>
                    </li>
                </ul>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <span>All is well!</span>
                <?php endif; ?>
            </div>

            <hr>
            <div class="mb-4">
                <h6>Generated Power</h6>
                <?php if(count($missingEnergySystemGeneratedPower) > 0): ?>
                <p>Add the 
                    <span> missing generated power </span> 
                    for these energy systems: 
                </p>
                <?php $__currentLoopData = $missingEnergySystemGeneratedPower; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingEnergySystemGenerated): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul class="list-unstyled">
                    <li class="d-flex" style="margin-top:9px">
                        <a class="btn btn-warning btn-sm" type="button" 
                            href="/energy-system/<?php echo e($missingEnergySystemGenerated->id); ?>/edit" target="_blank">
                            <span> <?php echo e($missingEnergySystemGenerated->name); ?> </span>   
                        </a>
                    </li>
                </ul>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <span>All is well!</span>
                <?php endif; ?>
            </div>

            <hr>
            <div class="mb-4">
                <h6>Turbine Power</h6>
                <?php if(count($missingEnergySystemTurbinePower) > 0): ?>
                <p>Add the 
                    <span> missing turbine power </span> 
                    for these energy systems: 
                </p>
                <?php $__currentLoopData = $missingEnergySystemTurbinePower; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingEnergySystemTurbine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul class="list-unstyled">
                    <li class="d-flex" style="margin-top:9px">
                        <a class="btn btn-warning btn-sm" type="button" 
                            href="/energy-system/<?php echo e($missingEnergySystemTurbine->id); ?>/edit" target="_blank">
                            <span> <?php echo e($missingEnergySystemTurbine->name); ?> </span>   
                        </a>
                    </li>
                </ul>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <span>All is well!</span>
                <?php endif; ?>
            </div>

            <hr>
            <div class="mb-4">
                <h6>Energy Components</h6>
                <?php if(count($missingEnergySystemPv) > 0): ?>
                <p>Add the 
                    <span> missing PV </span> 
                    for these energy systems: 
                </p>
                <?php $__currentLoopData = $missingEnergySystemPv; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingEnergySystemSolar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul class="list-unstyled">
                    <li class="d-flex" style="margin-top:9px">
                        <a class="btn btn-warning btn-sm" type="button" 
                            href="/energy-system/<?php echo e($missingEnergySystemSolar->id); ?>/edit" target="_blank">
                            <span> <?php echo e($missingEnergySystemSolar->name); ?> </span>   
                        </a>
                    </li>
                </ul>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                <br>
                <?php if(count($missingEnergySystemBattery) > 0): ?>
                <p>Add the 
                    <span> missing Batteries </span> 
                    for these energy systems: 
                </p>
                <?php $__currentLoopData = $missingEnergySystemBattery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingEnergySystemBatt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul class="list-unstyled">
                    <li class="d-flex" style="margin-top:9px">
                        <a class="btn btn-warning btn-sm" type="button" 
                            href="/energy-system/<?php echo e($missingEnergySystemBatt->id); ?>/edit" target="_blank">
                            <span> <?php echo e($missingEnergySystemBatt->name); ?> </span>   
                        </a>
                    </li>
                </ul>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </div>
        </div>
        <div class="timeline-event-time">Energy System</div>
    </div>
</li><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/internal/energy_system.blade.php ENDPATH**/ ?>