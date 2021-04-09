# Magento 2 Excellence NextGenImages
This Extension Provides JPGX and WEBP Support to Magento 2.

#### JPGX Images
The .jpgx file is a JPEG image (originally, .jpg) encrypted in a private cryptographic format with Acer eDataSecurity Management.
- This encryption decreases the size of file.
- Image quality will be same as Good as original one i.e; minimal quality loss.
- Make Website Faster.

#### WEBP Images

WebP is an emerging image format put out by Google. It’s designed to use a more aggressive and better optimized compression algorithm than JPG and PNG with the objective of reducing file sizes with minimal quality loss. And that means faster websites consuming less bandwidth. That makes your site’s visitors happy, and it also makes Google happy–they now explicitly favor fast websites over slow ones in their search rankings.

#### Features
NextGenImages Extension Avails the functionality to easily convert Jpg/Jpeg images to Jpgx and Jpg/Jpeg/Png Images to Webp formate.
- This results in website to load faster.

This Extension provides two option:
- First Jpg/Jpeg to Jpgx conversion.
- Second is converting all other image extension to webp conversion.
#### Note:If jpg to jpgx conversion is set to no and extension is enabled then all images get converted to webp formate.  

#### For Dependecy Librabry run below command in magento root directory
composer require rosell-dk/webp-convert:1.3.0

___________________________________________________________________________________________________

## Installation
#### Manual
- Download the Extension
- Place it in your Root/app/code.
- Make sure extension is Set.
- to check status of module run these commands on your magento root-
php bin/magento module:status-> this will show status of extension.
- If disbaled then run php bin/magento module:enable Excellence_NextGenImages.
- After that run these command given below:

php bin/magento setup:upgrade

php bin/magento setup:di:compile

php bin/magento setup:static-content:deploy

php bin/magento cache:clean

php bin/magento cache:flush

chmod -R 777 var/ pub/ generated

Extension is Installed Now.

Can check in Admin. 
___________________________________________________________________________________________________

## Screenshot

<a href="https://ibb.co/NnDGpRQ"><img src="https://i.ibb.co/1Jy4Kch/nextgenerationimg.png" alt="nextgenerationimg" border="0"></a>
