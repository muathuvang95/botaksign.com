<?php
/**
* Template Name: Test
*/

function nb_coppy_folder_from_s3($uri, $new_name = '') {
    if($new_name != '') {
        $awsAccessKey = get_option('nbdesigner_aws_access_key', false);
        $awsSecretKey = get_option('nbdesigner_aws_secret_key', false);
        $amazonRegion = get_option('nbdesigner_aws_region', false);
        $bucket = get_option('nbdesigner_aws_bucket', false);

        $s3 = new Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $amazonRegion,
            'credentials' => array(
                'key' => $awsAccessKey,
                'secret' => $awsSecretKey
            )
        ]);

        $uri = trim($uri, '/'). '/';

        $objects = $s3->getIterator('ListObjects', array('Bucket' => $bucket, 'Prefix' => $uri, 'Delimiter'=>'/'));
        foreach ($objects as $key => $object) {
            $path = $object['Key'];

            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $basename = basename($path);
            if($ext) {
                $uri_array = explode('/', $uri);
                if( count($uri_array) > 1 ) {
                    $uri_array[count($uri_array) - 2] = $new_name;
                    $path_new = implode('/' , $uri_array);
                    $res = $s3->copyObject([
                        'Bucket'     => $bucket,
                        'Key'        => "{$path_new}{$basename}",
                        'CopySource' => "{$bucket}/{$path}",
                        'ACL'        => "public-read-write"
                    ]);
                }
            }
            
        }
    }
}

nb_coppy_folder_from_s3('/reupload-design/01896861660756693/', 'muathuvang12313aw');

