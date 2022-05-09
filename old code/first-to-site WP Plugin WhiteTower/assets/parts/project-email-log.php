<?php 
global $post;
// Custom WP query email_query
$args_email_query = array(
    'post_type' => array('email'),
    'post_status' => array('publish'),
    'posts_per_page' => -1,
    'order' => 'DESC',
    'meta_query' => array(
        array(
            'key'     => 'related_project',
            'value'   => $post->ID,
            'compare' => '='
        )
    )
);

// Query all the email that related to this project
$email_query = get_posts( $args_email_query );
$email_logs = array();

if ( $email_query ) {
    foreach ( $email_query as $email) {
        
        $email_id = $email->ID;
        $email_title = get_the_title($email_id);
        $email_related_project = get_field('related_project', $email_id); // return post object
        $email_sla = get_field('sla', $email_id); // return post id
        $email_vendor = get_field('sla_vendor', $email_sla); // get sla vendor, return post object
        $email_logs[] = array(
            'id'    => $email_id,
            'title' => $email_title,
            'related_project' => $email_related_project->ID,
            'sla'   => $email_sla,
            'vendor'=> $email_vendor->ID,
            'link'  => get_edit_post_link($email->ID),
        );
    }

    wp_reset_postdata();
} 

if ( count($email_logs) > 1 ) {
    $count = 0;

    // Sort the email logs with vendor first then id
    usort($email_logs, function($a, $b) {
    $sort = $a['vendor'] <=> $b['vendor'];
    $sort .= $a['id'] <=> $b['id'];
    return $sort;
    });
    
    // echo '<pre>';
    // echo var_dump($email_logs);
    // echo '</pre>';
    ?>
    <div class="accordion" id="accordionEmail">

        <?php foreach ( $email_logs as $key => $log ) :?>
            <?php $nextlog = ($key + 1 < count($email_logs)) ? $email_logs[$key+1] : ''; ?>
            <?php $previoulog = ($key > 0) ? $email_logs[$key-1] : ''; ?>

            <?php if ($previoulog != '') $acc_start = ( $previoulog['vendor'] != $log['vendor']) ? true : false; ?>
            <?php if ($nextlog != '') $acc_end = ( $nextlog['vendor'] != $log['vendor']) ? true : false; ?>

            <!-- Check if it's start of the array or the new  -->
            <?php if ($key == 0 || $acc_start) : ?>
            <div class="accordion-item bg-white ">
                <h2 class="accordion-header mb-0" id="heading-<?php echo $count; ?>">
                <button
                    class="
                    accordion-button
                    relative
                    flex
                    items-center
                    w-full
                    pb-4
                    text-base text-gray-800 text-left
                    bg-white
                    border-0
                    rounded-none
                    transition
                    focus:outline-none
                    "
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapse-<?php echo $count; ?>"
                    aria-expanded="true"
                    aria-controls="collapse-<?php echo $count; ?>"
                >
                <?php echo get_the_title($log['vendor']); ?>
                </button>
                </h2>
                <div
                id="collapse-<?php echo $count; ?>"
                class="accordion-collapse collapse show"
                aria-labelledby="heading-<?php echo $count; ?>"
                data-bs-parent="#accordionEmail"
                >
                <div class="accordion-body py-4">
            <?php endif; ?>
            <!-- end of the accordion starter -->

                <?php if ($key == 0 || $acc_start) : ?>

                <div class="flex flex-col">
                    <div class="overflow-x-auto">
                        <div class="py-4 inline-block min-w-full">
                            <div class="overflow-hidden">
                                <table class="min-w-full text-center">
                                    <thead class="border-b" style="background: #3a3a3a;">
                                        <tr>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-4">
                                            #
                                        </th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-4">
                                            Title/Subject
                                        </th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-4">
                                            SLA
                                        </th>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-4">
                                            Status
                                        </th>
                                        <?php if( $log['link']!='' ) :?>
                                        <th scope="col" class="text-sm font-medium text-white px-6 py-4">
                                            
                                        </th>
                                        <?php endif; ?>
                                        </tr>
                                    </thead class="border-b">
                                    <tbody>
                <?php endif; ?>

                                        <tr class="bg-white border-b">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500 " ><?php echo $key+1; ?></td>
                                            <td class="text-sm text-gray-500 font-light px-6 py-4 whitespace-nowrap">
                                                <?php echo $log['title']; ?>
                                            </td>
                                            <td class="text-sm text-gray-500 font-light px-6 py-4 whitespace-nowrap">
                                                <?php echo get_the_title($log['sla']); ?>
                                            </td>
                                            <td class="text-sm text-gray-500 font-light px-6 py-4 whitespace-nowrap">
                                                <?php 
                                                $sla_status = get_field('sla-status', $log['id']); 
                                                switch ($sla_status) {
                                                    case 'late':
                                                        $sla_status_name = 'Late';
                                                        $sla_status_colour = 'red';
                                                    break;    
                                                    case 'on-time':
                                                        $sla_status_name = 'On time';
                                                        $sla_status_colour = 'green';
                                                    break;
                                                }
                                                ?>

                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?php echo $sla_status_colour; ?>-100 text-<?php echo $sla_status_colour; ?>-800">
                                                    <?php echo $sla_status_name; ?>
                                                </span>
                                                
                                            </td>
                                            <?php if( $log['link']!='' ) :?>

                                            <td class="text-sm text-gray-500 font-light px-6 py-4 whitespace-nowrap">
                                                <a href="<?php echo $log['link']; ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                                            </td>
                                            <?php endif; ?>
                                        </tr class="bg-white border-b">

                <?php if ($acc_end || $nextlog == '') : ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            
            <!-- Check if it's the end of the accordion -->
            <?php if ($acc_end || $nextlog == '') : ?>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <?php $count++; ?>
            <?php endif; ?>
            <!-- end of the accordion ender -->
        <?php endforeach; ?>
    </div>
    <!-- End of accordion section -->
    <?php
}
else {
    echo '<span>No related email(s).</span>';
}


?>