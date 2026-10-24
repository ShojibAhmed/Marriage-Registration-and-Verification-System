<?php
/**
 * Bangladesh Administrative Divisions, Districts, and Upazilas
 * Authoritative administrative dataset for academic prototype validation.
 */

function get_bangladesh_hierarchy() {
    return [
        'Dhaka' => [
            'Dhaka' => ['Dhanmondi', 'Mirpur', 'Gulshan', 'Uttara', 'Mohammadpur', 'Tejgaon', 'Motijheel', 'Savar', 'Keraniganj', 'Dhamrai', 'Nawabganj', 'Dohar'],
            'Gazipur' => ['Gazipur Sadar', 'Kaliakair', 'Kapasia', 'Sreepur', 'Kaliganj'],
            'Narayanganj' => ['Narayanganj Sadar', 'Araihazar', 'Bandar', 'Rupganj', 'Sonargaon'],
            'Narsingdi' => ['Narsingdi Sadar', 'Belabo', 'Monohardi', 'Palash', 'Raipura', 'Shibpur'],
            'Tangail' => ['Tangail Sadar', 'Basail', 'Bhuapur', 'Delduar', 'Ghatail', 'Gopalpur', 'Kalihati', 'Madhupur', 'Mirzapur', 'Nagarpur', 'Sakhipur'],
            'Faridpur' => ['Faridpur Sadar', 'Alfadanga', 'Bhanga', 'Boalmari', 'Charbhadrasan', 'Madhukhali', 'Nagarkanda', 'Sadarpur'],
            'Manikganj' => ['Manikganj Sadar', 'Daulatpur', 'Ghior', 'Harirampur', 'Saturia', 'Shivalaya', 'Singair'],
            'Munshiganj' => ['Munshiganj Sadar', 'Gazaria', 'Lohajang', 'Sirajdikhan', 'Sreenagar', 'Tongibari'],
            'Kishoreganj' => ['Kishoreganj Sadar', 'Bajitpur', 'Bhairab', 'Hossainpur', 'Itna', 'Karimganj', 'Katiadi', 'Kuliarchar', 'Mithamain', 'Nikli', 'Pakundia', 'Tarail'],
            'Gopalganj' => ['Gopalganj Sadar', 'Kashiani', 'Kotalipara', 'Muksudpur', 'Tungipara'],
            'Madaripur' => ['Madaripur Sadar', 'Kalkini', 'Rajoir', 'Shibchar'],
            'Rajbari' => ['Rajbari Sadar', 'Baliakandi', 'Goalandaghat', 'Pangsha', 'Kalukhali'],
            'Shariatpur' => ['Shariatpur Sadar', 'Bhedarganj', 'Damudya', 'Gosairhat', 'Naria', 'Zajira']
        ],
        'Chattogram' => [
            'Chattogram' => ['Kotwali', 'Panchlaish', 'Pahartali', 'Halishahar', 'Bakalia', 'Anwara', 'Banshkhali', 'Boalkhali', 'Chandanaish', 'Fatikchhari', 'Hathazari', 'Lohagara', 'Mirsharai', 'Patiya', 'Rangunia', 'Raozan', 'Sandwip', 'Satkania', 'Sitakunda'],
            'Cox\'s Bazar' => ['Cox\'s Bazar Sadar', 'Chakaria', 'Kutubdia', 'Maheshkhali', 'Ramu', 'Teknaf', 'Ukhia', 'Pekua'],
            'Cumilla' => ['Cumilla Adarsha Sadar', 'Barura', 'Brahmanpara', 'Burichang', 'Chandina', 'Chauddagram', 'Daudkandi', 'Debidwar', 'Homna', 'Laksam', 'Muradnagar', 'Nangalkot', 'Titas', 'Meghna', 'Monohargonj'],
            'Feni' => ['Feni Sadar', 'Chhagalnaiya', 'Daganbhuiyan', 'Parshuram', 'Fulgazi', 'Sonagazi'],
            'Brahmanbaria' => ['Brahmanbaria Sadar', 'Ashuganj', 'Bancharampur', 'Kasba', 'Nabinagar', 'Nasirnagar', 'Sarail', 'Akhaura', 'Bijoynagar'],
            'Noakhali' => ['Noakhali Sadar', 'Begumganj', 'Chatkhil', 'Companiganj', 'Hatiya', 'Senbagh', 'Subarnachar', 'Kabirhat', 'Sonaimuri'],
            'Chandpur' => ['Chandpur Sadar', 'Faridganj', 'Haimchar', 'Haziganj', 'Kachua', 'Matlab Dakshin', 'Matlab Uttar', 'Shahrasti'],
            'Lakshmipur' => ['Lakshmipur Sadar', 'Raipur', 'Ramganj', 'Ramgati', 'Kamalnagar'],
            'Rangamati' => ['Rangamati Sadar', 'Bagaichhari', 'Barkal', 'Kaptai', 'Juraichhari', 'Langadu', 'Naniarchar', 'Rajasthali', 'Belaichhari'],
            'Khagrachhari' => ['Khagrachhari Sadar', 'Dighinala', 'Lakshmichhari', 'Mahalchhari', 'Manikchhari', 'Matiranga', 'Panchhari', 'Ramgarh'],
            'Bandarban' => ['Bandarban Sadar', 'Alikadam', 'Lama', 'Naikhongchhari', 'Rowangchhari', 'Ruma', 'Thanchi']
        ],
        'Rajshahi' => [
            'Rajshahi' => ['Boalia', 'Motihar', 'Rajpara', 'Shah Makhdum', 'Bagha', 'Bagmara', 'Charghat', 'Durgapur', 'Godagari', 'Mohanpur', 'Paba', 'Puthia', 'Tanore'],
            'Bogura' => ['Bogura Sadar', 'Adamdighi', 'Dhunat', 'Dhupchanchia', 'Gabtali', 'Kahaloo', 'Nandigram', 'Sariakandi', 'Shajahanpur', 'Sherpur', 'Shibganj', 'Sonatala'],
            'Pabna' => ['Pabna Sadar', 'Atgharia', 'Bera', 'Bhangura', 'Chatmohar', 'Faridpur', 'Ishwardi', 'Santhia', 'Sujanagar'],
            'Sirajganj' => ['Sirajganj Sadar', 'Belkuchi', 'Chauhali', 'Kamarkhanda', 'Kazipur', 'Raiganj', 'Shahjadpur', 'Tarash', 'Ullapara'],
            'Naogaon' => ['Naogaon Sadar', 'Atrai', 'Badalgachhi', 'Dhamoirhat', 'Manda', 'Mohadevpur', 'Niamatpur', 'Patnitala', 'Porsha', 'Raninagar', 'Sapahar'],
            'Natore' => ['Natore Sadar', 'Bagatipara', 'Baraigram', 'Gurudaspur', 'Lalpur', 'Singra', 'Naldanga'],
            'Joypurhat' => ['Joypurhat Sadar', 'Akkelpur', 'Kalai', 'Khetlal', 'Panchbibi'],
            'Chapainawabganj' => ['Chapainawabganj Sadar', 'Bholahat', 'Gomastapur', 'Nachole', 'Shibganj']
        ],
        'Khulna' => [
            'Khulna' => ['Khulna Sadar', 'Daulatpur', 'Khalishpur', 'Sonadanga', 'Batiaghata', 'Dacope', 'Dumuria', 'Dighalia', 'Koyra', 'Paikgachha', 'Phultala', 'Rupsha', 'Terokhada'],
            'Jashore' => ['Jashore Sadar', 'Abhaynagar', 'Bagherpara', 'Chaugachha', 'Jhikargachha', 'Keshabpur', 'Manirampur', 'Sharsha'],
            'Kushtia' => ['Kushtia Sadar', 'Bheramara', 'Daulatpur', 'Khoksa', 'Kumarkhali', 'Mirpur'],
            'Jhenaidah' => ['Jhenaidah Sadar', 'Harinakunda', 'Kaliganj', 'Kotchandpur', 'Maheshpur', 'Shailkupa'],
            'Chuadanga' => ['Chuadanga Sadar', 'Alamdanga', 'Damurhuda', 'Jibannagar'],
            'Meherpur' => ['Meherpur Sadar', 'Gangni', 'Mujibnagar'],
            'Satkhira' => ['Satkhira Sadar', 'Assasuni', 'Debhata', 'Kalaroa', 'Kaliganj', 'Shyamnagar', 'Tala'],
            'Bagerhat' => ['Bagerhat Sadar', 'Chitalmari', 'Fakirhat', 'Kachua', 'Mollahat', 'Mongla', 'Morrelganj', 'Rampal', 'Sarankhola'],
            'Narail' => ['Narail Sadar', 'Kalia', 'Lohagara'],
            'Magura' => ['Magura Sadar', 'Mohammadpur', 'Shalikha', 'Sreepur']
        ],
        'Barishal' => [
            'Barishal' => ['Barishal Sadar', 'Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gaurnadi', 'Hizla', 'Mehendiganj', 'Muladi', 'Wazirpur'],
            'Patuakhali' => ['Patuakhali Sadar', 'Bauphal', 'Dashmina', 'Galachipa', 'Kalapara', 'Mirzaganj', 'Rangabali', 'Dumki'],
            'Bhola' => ['Bhola Sadar', 'Burhanuddin', 'Char Fasson', 'Daulatkhan', 'Lalmohan', 'Manpura', 'Tazumuddin'],
            'Pirojpur' => ['Pirojpur Sadar', 'Bhandaria', 'Kawkhali', 'Mathbaria', 'Nazirpur', 'Nesarabad (Swarupkati)', 'Indurkani'],
            'Barguna' => ['Barguna Sadar', 'Amtali', 'Bamna', 'Betagi', 'Patharghata', 'Taltali'],
            'Jhalokati' => ['Jhalokati Sadar', 'Kathalia', 'Nalchity', 'Rajapur']
        ],
        'Sylhet' => [
            'Sylhet' => ['Sylhet Sadar', 'Beanibazar', 'Bishwanath', 'Dakshin Surma', 'Fenchuganj', 'Golapganj', 'Gowainghat', 'Jaintiapur', 'Kanaighat', 'Companiganj', 'Zakiganj'],
            'Moulvibazar' => ['Moulvibazar Sadar', 'Barlekha', 'Juri', 'Kamalganj', 'Kulaura', 'Rajnagar', 'Sreemangal'],
            'Habiganj' => ['Habiganj Sadar', 'Ajmiriganj', 'Bahubal', 'Baniachong', 'Chunarughat', 'Lakhai', 'Madhabpur', 'Nabiganj', 'Sayestaganj'],
            'Sunamganj' => ['Sunamganj Sadar', 'Bishwamvarpur', 'Chhatak', 'Derai', 'Dharampasha', 'Dowarabazar', 'Jagannathpur', 'Jamalganj', 'Shantiganj', 'Tahirpur']
        ],
        'Rangpur' => [
            'Rangpur' => ['Rangpur Sadar', 'Badarganj', 'Gangachhara', 'Kaunia', 'Mithapukur', 'Pirgachha', 'Pirganj', 'Taraganj'],
            'Dinajpur' => ['Dinajpur Sadar', 'Birampur', 'Birganj', 'Biral', 'Bochaganj', 'Chirirbandar', 'Phulbari', 'Ghoraghat', 'Hakimpur', 'Kaharole', 'Khansama', 'Nawabganj', 'Parbatipur'],
            'Gaibandha' => ['Gaibandha Sadar', 'Fulchhari', 'Gobindaganj', 'Palashbari', 'Sadullapur', 'Saghata', 'Sundarganj'],
            'Kurigram' => ['Kurigram Sadar', 'Bhurungamari', 'Char Rajibpur', 'Chilmari', 'Nageshwari', 'Phulbari', 'Rajarhat', 'Raomari', 'Ulipur'],
            'Lalmonirhat' => ['Lalmonirhat Sadar', 'Aditmari', 'Hatibandha', 'Kaliganj', 'Patgram'],
            'Nilphamari' => ['Nilphamari Sadar', 'Dimla', 'Domar', 'Jaldhaka', 'Kishoreganj', 'Syedpur'],
            'Panchagarh' => ['Panchagarh Sadar', 'Atwari', 'Boda', 'Debiganj', 'Tetulia'],
            'Thakurgaon' => ['Thakurgaon Sadar', 'Baliadangi', 'Haripur', 'Pirganj', 'Ranisankhail']
        ],
        'Mymensingh' => [
            'Mymensingh' => ['Mymensingh Sadar', 'Bhaluka', 'Dhobaura', 'Fulbaria', 'Gafargaon', 'Gauripur', 'Haluaghat', 'Ishwarganj', 'Muktagachha', 'Nandail', 'Phulpur', 'Tara Khanda'],
            'Jamalpur' => ['Jamalpur Sadar', 'Bakshiganj', 'Dewanganj', 'Islampur', 'Madarganj', 'Melandaha', 'Sarishabari'],
            'Netrokona' => ['Netrokona Sadar', 'Atpara', 'Barhatta', 'Durgapur', 'Khaliajuri', 'Kalmakanda', 'Kendua', 'Madan', 'Mohanganj', 'Purbadhala'],
            'Sherpur' => ['Sherpur Sadar', 'Jhenaigati', 'Nakla', 'Nalitabari', 'Sreebardi']
        ]
    ];
}

function get_divisions() {
    return array_keys(get_bangladesh_hierarchy());
}

function get_districts($division) {
    $geo = get_bangladesh_hierarchy();
    return isset($geo[$division]) ? array_keys($geo[$division]) : [];
}

function get_upazilas($division, $district) {
    $geo = get_bangladesh_hierarchy();
    return isset($geo[$division][$district]) ? $geo[$division][$district] : [];
}

/**
 * Validates that district belongs to division and upazila belongs to district.
 */
function is_valid_location($division, $district, $upazila) {
    $geo = get_bangladesh_hierarchy();
    if (!isset($geo[$division])) {
        return false;
    }
    if (!isset($geo[$division][$district])) {
        return false;
    }
    if (!in_array($upazila, $geo[$division][$district], true)) {
        return false;
    }
    return true;
}

/**
 * Generate 3-letter district prefix for Kazi License.
 * Examples: Dhaka -> Dha, Chuadanga -> Chu, Bogura -> Bog.
 */
function get_district_prefix($district) {
    // Strip non-alpha characters (like apostrophes in Cox's Bazar)
    $clean = preg_replace('/[^a-zA-Z]/', '', $district);
    $prefix = substr($clean, 0, 3);
    return ucfirst(strtolower($prefix));
}
