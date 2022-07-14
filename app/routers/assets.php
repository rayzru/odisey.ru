<?php
namespace odissey;

use Intervention\Image\ImageManagerStatic as Image;

$this->respond(
    'GET',
    '/images/catalog/_cache/[i:width]x[i:height]/[:l1]/[:l2]/[:filename]' .
    '[-grayscale:grayscale]?.[jpe?g|JPG|gif|png:extension]',
    function ($request, $response, $service, $app) {

        $root = implode(DIRECTORY_SEPARATOR, [$_SERVER['DOCUMENT_ROOT'], 'assets', 'images', 'catalog']);

        $originalImage = implode(
            DIRECTORY_SEPARATOR,
            [
                $root,
                $request->l1,
                $request->l2,
                $request->filename . '.' . $request->extension,
            ]
        );

        if (!file_exists($originalImage)) {
            $response->code(404);
            return 'not found';
        }

        $resizedPath = implode(DIRECTORY_SEPARATOR, [
            $root,
            '_cache',
            $request->width . 'x' . $request->height,
            $request->l1,
            $request->l2,
        ]);

        $effectGrayscale = $request->grayscale ? $request->grayscale : '';

        $resizedImage = implode(
            DIRECTORY_SEPARATOR,
            [
                $resizedPath,
                $request->filename .
                $effectGrayscale . '.' . $request->extension,
            ]
        );

        if (file_exists($originalImage) && !file_exists($resizedImage)) {
            if (!Helpers::mkpath($resizedPath, true)) {
                echo ('cannot create directory');
                die();
            }

            $img = Image::make($originalImage)
                ->resize($request->width, $request->height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            //    ->insert(implode(DIRECTORY_SEPARATOR, [$root, '..', 'watermark.png']))
            if ($effectGrayscale) {
                $img->greyscale()->brightness(35);
            }
            $img->save($resizedImage);
            $response->redirect($request->uri() . '?new')->send();
        }
        die();
    }
);

$this->respond('POST', '/images/catalog/upload', function ($request, $response, $service, $app) {
    $img = Image::make($_FILES['image']['tmp_name']);
});
