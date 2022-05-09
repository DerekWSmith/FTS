<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <h3 class="project-summary-title ">Location Information</h3>
    </div>

    <?php if ( have_rows( 'location' ) ) : ?>
        <?php while ( have_rows( 'location' ) ) :
            the_row(); ?>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Lot No.</p>
                <p>
                    <?php if ( $lot_no = get_sub_field( 'lot_no' ) ) : ?>
                            <?php echo esc_html( $lot_no ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Street No.</p>
                <p>
                    <?php if ( $street_no = get_sub_field( 'street_no' ) ) : ?>
                        <?php echo esc_html( $street_no ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Street Name</p>
                <p>
                    <?php if ( $street_name = get_sub_field( 'street_name' ) ) : ?>
                        <?php echo esc_html( $street_name ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Street Type</p>
                <p>
                    <?php if ( $street_type = get_sub_field( 'street_type' ) ) : ?>
                        <?php echo esc_html( $street_type ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Postcode</p>
                <p>
                    <?php if ( $postcode = get_sub_field( 'postcode' ) ) : ?>
                        <?php echo esc_html( $postcode ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">State</p>
                <p>
                    <?php if ( $state = get_sub_field( 'state' ) ) : ?>
                        <?php echo esc_html( $state ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Estate</p>
                <p>
                    <?php if ( $estate = get_sub_field( 'estate' ) ) : ?>
                        <?php echo esc_html( $estate ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Division</p>
                <p>
                    <?php if ( $division = get_sub_field( 'division' ) ) : ?>
                        <?php echo esc_html( $division ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Reference No.</p>
                <p>
                    <?php if ( $reference_no = get_sub_field( 'reference_no' ) ) : ?>
                        <?php echo esc_html( $reference_no ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Purchase Order</p>
                <p>
                    <?php if ( $purchase_order = get_sub_field( 'purchase_order' ) ) : ?>
                        <?php echo esc_html( $purchase_order ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Floor Area</p>
                <p>
                    <?php if ( $floor_area = get_sub_field( 'floor_area' ) ) : ?>
                        <?php echo esc_html( $floor_area ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Allotment Area</p>
                <p>
                    <?php if ( $allotment_area = get_sub_field( 'allotment_area' ) ) : ?>
                        <?php echo esc_html( $allotment_area ); ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>


<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <h3 class="project-summary-title">House Information</h3>
    </div>
    <?php if ( have_rows( 'house_information' ) ) : ?>
        <?php while ( have_rows( 'house_information' ) ) :
            the_row(); ?>
            
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">House Type</p>
                <p>
                    <?php if ( $house_type = get_sub_field( 'house_type' ) ) : ?>
                        <?php echo esc_html( $house_type ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">No. of Dweillings</p>
                <p>
                    <?php if ( $no_of_dwellings = get_sub_field( 'no_of_dwellings' ) ) : ?>
                        <?php echo esc_html( $no_of_dwellings ); ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Facade Type</p>
                <p>
                    <?php if ( $facade_type = get_sub_field( 'facade_type' ) ) : ?>
                        <?php echo esc_html( $facade_type ); ?>
                    <?php endif; ?>
                </p>
            </div>

        <?php endwhile; ?>
    <?php endif; ?>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <h3 class="project-summary-title">Owner information</h3>
    </div>

    <?php if ( have_rows( 'owner_information' ) ) : ?>

        <?php while ( have_rows( 'owner_information' ) ) :
            the_row(); ?>
        
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">First Name</p>
                <p>
                    <?php if ( $owner_first_name = get_sub_field( 'owner_first_name' ) ) : ?>
                        <?php echo esc_html( $owner_first_name ); ?>
                    <?php endif; ?>
                </p>
            </div>
                        
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Middle Name</p>
                <p>
                    <?php if ( $owner_middle_name = get_sub_field( 'owner_middle_name' ) ) : ?>
                        <?php echo esc_html( $owner_middle_name ); ?>
                    <?php endif; ?>
                </p>
            </div>
    
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Last Name</p>
                <p>
                    <?php if ( $owner_last_name = get_sub_field( 'owner_last_name' ) ) : ?>
                        <?php echo esc_html( $owner_last_name ); ?>
                    <?php endif; ?>
                </p>
            </div>
                        
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Company Name</p>
                <p>
                    <?php if ( $owner_company_name = get_sub_field( 'owner_company_name' ) ) : ?>
                        <?php echo esc_html( $owner_company_name ); ?>
                    <?php endif; ?>
                </p>
            </div>
    
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Street No.</p>
                <p>
                    <?php if ( $owner_street_no = get_sub_field( 'owner_street_no' ) ) : ?>
                        <?php echo esc_html( $owner_street_no ); ?>
                    <?php endif; ?>
                </p>
            </div>
    
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Street Name</p>
                <p>
                    <?php if ( $owner_street_name = get_sub_field( 'owner_street_name' ) ) : ?>
                        <?php echo esc_html( $owner_street_name ); ?>
                    <?php endif; ?>
                </p>
            </div>
    
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Street Type</p>
                <p>
                    <?php if ( $owner_street_type = get_sub_field( 'owner_street_type' ) ) : ?>
                        <?php echo esc_html( $owner_street_type ); ?>
                    <?php endif; ?>
                </p>
            </div>
    
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Suburb</p>
                <p>
                    <?php if ( $owner_suburb = get_sub_field( 'owner_suburb' ) ) : ?>
                        <?php echo esc_html( $owner_suburb ); ?>
                    <?php endif; ?>
                </p>
            </div>
    
            <div class="col-span-1 pb-4">
                <p class="font-bold pb-4">Postcode</p>
                <p>
                    <?php if ( $owner_postcode = get_sub_field( 'owner_postcode' ) ) : ?>
                        <?php echo esc_html( $owner_postcode ); ?>
                    <?php endif; ?>
                </p>
            </div>
    
        <?php endwhile; ?>
    <?php endif; ?>
    
</div>