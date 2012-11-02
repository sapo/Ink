#!/bin/bash

ink_version="v1"
ink_zip_filename="ink-${ink_version}.zip"

script_dir_path=`readlink -f $0 | sed 's,^\(.*/\)\?\([^/]*\),\1,' `;

ink_ink_dir="${script_dir_path}../ink/"

ink_less_dir="${ink_ink_dir}less/"
ink_css_dir="${ink_ink_dir}css/"
ink_font_dir="${ink_ink_dir}font/"
ink_imgs_dir="${ink_ink_dir}imgs/"
ink_demo_file="${ink_ink_dir}my-page.html"
ink_demo_cdn_file="${ink_ink_dir}my-cdn-page.html"

ink_to_prod_dir="${ink_ink_dir}ink_to_prod_dir"

file_less_ink="${ink_less_dir}ink.less"
file_less_ink_ie="${ink_less_dir}ink-ie.less"

file_css_ink="${ink_css_dir}ink.css"
file_css_ink_ie="${ink_css_dir}ink-ie.css"

file_css_min_ink="${ink_css_dir}ink-min.css"
file_css_min_ink_ie="${ink_css_dir}ink-ie-min.css"

echo "Script running on: $script_dir_path";

# starts here 

cd $ink_css_dir;

echo "Remove old CSS";

#rm *.css

cd $ink_ink_dir;

echo "Making CSS $file_css_ink"
#recess --compile $file_less_ink > $file_css_ink
lessc $file_less_ink > $file_css_ink
echo "Done"

echo "Making CSS $file_css_min_ink"
#recess --compile --compress $file_less_ink > $file_css_min_ink
lessc --yui-compress $file_less_ink > $file_css_min_ink
echo "Done"

echo "Making CSS $file_css_ink_ie"
#recess --compile $file_less_ink_ie > $file_css_ink_ie
less $file_less_ink_ie > $file_css_ink_ie
echo "Done"

echo "Making CSS $file_css_min_ink_ie"
#recess --compile --compress $file_less_ink_ie > $file_css_min_ink_ie
lessc --yui-compress $file_less_ink_ie > $file_css_min_ink_ie
echo "Done"

if [ -d $ink_to_prod_dir ]; then 
    echo "exists... lets delete it"
    rm -rf $ink_to_prod_dir;
fi

mkdir -p "${ink_to_prod_dir}/${ink_version}";

echo "Copy files to go live dir"
cp -r "${ink_less_dir}" "${ink_to_prod_dir}/${ink_version}"
cp -r "${ink_css_dir}" "${ink_to_prod_dir}/${ink_version}"
cp -r "${ink_font_dir}" "${ink_to_prod_dir}/${ink_version}"
cp -r "${ink_imgs_dir}" "${ink_to_prod_dir}/${ink_version}"
cp -r "${ink_demo_file}" "${ink_to_prod_dir}/${ink_version}"
cp -r "${ink_demo_cdn_file}" "${ink_to_prod_dir}/${ink_version}"

echo "Done...";



cd "${ink_to_prod_dir}/"

echo "Making ZIP file"

cd $ink_version

zip -r ${ink_zip_filename} *.html css less font imgs 

cd ..

echo "Done...";

echo 
echo "Start to send to production" 
echo 

for i in `echo "10.135.150.21 10.135.150.22 10.135.150.23"`; do 
    echo 
    echo "Sending to: $i"; 
    rsync --recursive --links --delete --verbose ${ink_version} rsync://$i/cssink ; 
    echo 
done

#rsync --recursive --links --delete --verbose ${ink_version} rsync://10.135.150.21/cssink

echo 
echo "Done..."
echo 





