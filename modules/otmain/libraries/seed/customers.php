<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Seed customer catalog — one row per client.
 * Match by `company` (exact) when linking documents.
 *
 * @return array<int, array{nr:int,company:string,address:string,zip:string,city:string,phone:string,email:string,country_iso:string}>
 */
return [
    ['nr' => 1,  'company' => 'CemFlexX B.V.', 'address' => 'Pauvreweg 27', 'zip' => '4879NJ', 'city' => 'Etten-Leur', 'phone' => '+31 (0)76 850 39 04', 'email' => 'finance@cemflexx-int.org', 'country_iso' => 'NL'],
    ['nr' => 2,  'company' => 'Suriname Shiphandling & Services NV', 'address' => 'Ds Martin Luther Kingweg 8-9', 'zip' => '', 'city' => 'Paramaribo', 'phone' => '+597-8532726', 'email' => 'jerrel@rudisa.net', 'country_iso' => 'SR'],
    ['nr' => 3,  'company' => 'Belastingdienst', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 4,  'company' => 'Yoyo Transport', 'address' => '32 Av. Villemain', 'zip' => '75014', 'city' => 'Paris', 'phone' => '', 'email' => 'yoyotransport@yahoo.com', 'country_iso' => 'FR'],
    ['nr' => 5,  'company' => 'Bol.com', 'address' => 'Papendorpseweg 100', 'zip' => '3528 BJ', 'city' => 'Utrecht', 'phone' => '088 712 60 00', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 6,  'company' => 'OT-Main', 'address' => 'Bajonetstraat 52', 'zip' => '3014 ZK', 'city' => 'Rotterdam', 'phone' => '+31647239658', 'email' => 'info@otmain.com', 'country_iso' => 'NL'],
    ['nr' => 7,  'company' => 'TP Company Limited', 'address' => 'Bumbwini, P.O BOX 271', 'zip' => '', 'city' => 'Zanzibar', 'phone' => '+255242230722', 'email' => 'procurementmanager@turkyspetroleum.co.tz', 'country_iso' => 'TZ'],
    ['nr' => 8,  'company' => 'WM Industrietechnik Isaak Öztürk & Oliver Schmidt GbR', 'address' => 'Südlohner Weg 34a', 'zip' => 'D-48703', 'city' => 'Stadtlohn', 'phone' => '+49 2563 2098550', 'email' => 'info@wm-industrietechnik.de', 'country_iso' => 'DE'],
    ['nr' => 9,  'company' => 'Boettcher Conveying Systems & Service GmbH', 'address' => 'Theodor-Marwitz-Street 2a', 'zip' => '21337', 'city' => 'Lüneburg', 'phone' => '+49 4131 2213 100', 'email' => 'boe@power-in-motion.net', 'country_iso' => 'DE'],
    ['nr' => 10, 'company' => 'Handelsmij SPT b.v.', 'address' => 'Rudonk 21', 'zip' => '4824 AJ', 'city' => 'Breda', 'phone' => '+31850660100', 'email' => 'info@smitspt.nl', 'country_iso' => 'NL'],
    ['nr' => 11, 'company' => 'Doedijns b.v.', 'address' => 'Bleiswijkseweg 51', 'zip' => '2712 PB', 'city' => 'Zoetermeer', 'phone' => '+31880912600', 'email' => 'info@doedijns.com', 'country_iso' => 'NL'],
    ['nr' => 12, 'company' => 'Projectservice Nederland B.V.', 'address' => 'Darwin 20', 'zip' => '7609RL', 'city' => 'Almelo', 'phone' => '+31854012499', 'email' => 'info@projectservice.nl', 'country_iso' => 'NL'],
    ['nr' => 13, 'company' => 'Remote Control Parts B.V.', 'address' => 'Industrieweg 20', 'zip' => '4794 SX', 'city' => 'Heijningen', 'phone' => '+31167521228', 'email' => 'info@remotecontrolparts.nl', 'country_iso' => 'NL'],
    ['nr' => 14, 'company' => 'Ayushman Freelancer', 'address' => 'Santoshi Mishra, E-233, Pariwar Passion apartment', 'zip' => '', 'city' => 'Bangalore', 'phone' => '+918587006726', 'email' => '', 'country_iso' => 'IN'],
    ['nr' => 15, 'company' => 'Interfilter Industries B.V', 'address' => 'Seggeweg 2', 'zip' => '3237 MK', 'city' => 'Vierpolders', 'phone' => '+31181 - 31 11 87', 'email' => 'info@interfilter.nl', 'country_iso' => 'NL'],
    ['nr' => 16, 'company' => 'Distrimex Pompen & Service BV', 'address' => 'Edisonstraat 12', 'zip' => '7006 RD', 'city' => 'Doetinchem', 'phone' => '+31 (0)314 36 84 44', 'email' => 'info@distrimex.nl', 'country_iso' => 'NL'],
    ['nr' => 17, 'company' => 'Nanjing Deers Industrial Co., Ltd', 'address' => 'Hanzhong Road No. 185, Qinhuai Dist, Nanjing City', 'zip' => '', 'city' => 'Jiangsu Province', 'phone' => '+86 25 8450 7790', 'email' => 'sellers1@chinarubberfender.com', 'country_iso' => 'CN'],
    ['nr' => 18, 'company' => 'Pov Fluid Control Technology (Wuhu) Co., Ltd', 'address' => 'No.6 Weishier Road, Yijiang Dist. Wuhu City', 'zip' => '', 'city' => 'Anhui Province', 'phone' => '+86 18616895255', 'email' => 'pov@povvalve.com', 'country_iso' => 'CN'],
    ['nr' => 19, 'company' => 'Tanjung Agus Fastwork', 'address' => 'Karanganyar', 'zip' => '', 'city' => 'Jawa Tengah', 'phone' => '+6286921693226', 'email' => 'tanjungagus999@gmail.com', 'country_iso' => 'ID'],
    ['nr' => 20, 'company' => 'Sylvano Fastwork', 'address' => 'Bekasi', 'zip' => '', 'city' => 'Jawa Barat', 'phone' => '+62895399399932', 'email' => '', 'country_iso' => 'ID'],
    ['nr' => 21, 'company' => 'DHL Nederlands', 'address' => 'Amsterdam', 'zip' => '', 'city' => 'Amsterdam', 'phone' => '+3188-055 2000', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 22, 'company' => 'B.V. VEGA', 'address' => 'Arnhemseweg-Zuid 213-2', 'zip' => '3817 CG', 'city' => 'Amersfoort', 'phone' => '(033) 450 25 02', 'email' => 'info.nl@vega.com', 'country_iso' => 'NL'],
    ['nr' => 23, 'company' => 'Parcop s.r.l.', 'address' => 'Via filomarino III trav N 13', 'zip' => '80070', 'city' => 'Monte di Procida Napoli', 'phone' => '39 081 868 2064', 'email' => '', 'country_iso' => 'IT'],
    ['nr' => 24, 'company' => 'LabelsDirect BV', 'address' => 'Trasmolenlaan 12', 'zip' => '3447 GZ', 'city' => 'Woerden', 'phone' => '0348 342 186', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 25, 'company' => 'Verpakgigant.nl', 'address' => 'De Schrepel 24 B1', 'zip' => '1648 GC', 'city' => 'De Goorn', 'phone' => '', 'email' => 'support@verpakgigant.nl', 'country_iso' => 'NL'],
    ['nr' => 26, 'company' => 'Amazon EU S.à r.l', 'address' => 'Mr. Treublaan 7', 'zip' => '1097 DP', 'city' => 'Amsterdam', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 27, 'company' => 'Fastwork.id', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '+62 821-6747-1450', 'email' => 'support@fastwork.id', 'country_iso' => 'ID'],
    ['nr' => 28, 'company' => 'Dongguan Dxseals Technology Co.,Ltd', 'address' => '56 Dongcheng Road, Guancheng District, Dongguan', 'zip' => '', 'city' => 'Dongguan', 'phone' => '+86 15992798689', 'email' => 'sales1@dxtseals.com', 'country_iso' => 'CN'],
    ['nr' => 29, 'company' => 'Unique Transmission Equipment (Luoyang) Co., Ltd.', 'address' => 'No.22 Binhe Road, New & High Tech Industry Development Zone', 'zip' => '471000', 'city' => 'Luoyang', 'phone' => '+86 0379 64915181', 'email' => '', 'country_iso' => 'CN'],
    ['nr' => 30, 'company' => 'SHENZHEN WETAC TECHNOLOGY CO.,LTD', 'address' => 'ROOM 106, BUILDING 1, NO. 5 NIUXING ROAD', 'zip' => '', 'city' => 'Dongguan, Guangdong', 'phone' => '86-13530046228', 'email' => 'atlastrade@163.com', 'country_iso' => 'CN'],
    ['nr' => 31, 'company' => 'V-Trust Inspection Service Group', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '+86-20-89089938', 'email' => 'cathy.xiao@v-trust.com', 'country_iso' => 'CN'],
    ['nr' => 32, 'company' => 'FS International Limited Cargo', 'address' => 'I/F Block C Sea View Estate, No.8 Watson Road, North Point', 'zip' => '', 'city' => 'Hong Kong', 'phone' => '85228400824', 'email' => '', 'country_iso' => 'HK'],
    ['nr' => 33, 'company' => 'PT. Trinity Konsultan Group', 'address' => 'Jl. Gn Saputan No.1A, Pemecutan Kelod, Denpasar Barat', 'zip' => '', 'city' => 'Bali', 'phone' => '082341878520', 'email' => 'trinity.konsultangroup@gmail.com', 'country_iso' => 'ID'],
    ['nr' => 34, 'company' => 'Rubix B.V', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 35, 'company' => 'Hydrotechnik24.de', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'DE'],
    ['nr' => 36, 'company' => 'Automation24 GmbH', 'address' => 'Keurenplein 41', 'zip' => '1069CD', 'city' => 'Amsterdam', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 37, 'company' => 'Jorny Product B.V', 'address' => 'Philipshoofjesweg 90', 'zip' => '3247 XS', 'city' => 'Dirksland', 'phone' => '+31 (0)6 51 95 10 96', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 38, 'company' => 'Hydraunica B.V.', 'address' => 'Biesbosweg 2', 'zip' => '5145 PZ', 'city' => 'Waalwijk', 'phone' => '+31 318 519 837', 'email' => 'j.denhertog@hydraunica.nl', 'country_iso' => 'NL'],
    ['nr' => 39, 'company' => 'RR Holland', 'address' => 'Energieweg 34', 'zip' => '4906CG', 'city' => 'Oosterhout', 'phone' => '+31162456397', 'email' => 'sales@rrholland.nl', 'country_iso' => 'NL'],
    ['nr' => 40, 'company' => 'Witway Webshops B.V', 'address' => 'Tussendiepen 48', 'zip' => '9206AE', 'city' => 'Drachten', 'phone' => '0850020030', 'email' => 'klantenservice@witway.nl', 'country_iso' => 'NL'],
    ['nr' => 41, 'company' => 'Klium N.V', 'address' => 'Ekkelgaarden 26', 'zip' => '3500', 'city' => 'Hasselt', 'phone' => '', 'email' => '', 'country_iso' => 'BE'],
    ['nr' => 42, 'company' => 'Meuth', 'address' => '', 'zip' => '', 'city' => '', 'phone' => '', 'email' => '', 'country_iso' => 'DE'],
    ['nr' => 43, 'company' => 'Scheepvaartcenter', 'address' => 'Krammer 8', 'zip' => '3232 HE', 'city' => 'Brielle', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 44, 'company' => 'Shenzen Rongtai Automation Technology Co.Ltd', 'address' => 'Room 401, No. 29-1, Xintangkeng Road, Silian Community, Longgang District', 'zip' => '', 'city' => 'Shenzhen', 'phone' => '', 'email' => '', 'country_iso' => 'CN'],
    ['nr' => 45, 'company' => 'Outletspecialist BV', 'address' => 'Zuidhollandsedijk 179', 'zip' => '5171 TM', 'city' => 'Kaatsheuvel', 'phone' => '', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 46, 'company' => 'RS Components BV', 'address' => 'Bingerweg 19', 'zip' => '2001 HN', 'city' => 'Haarlem', 'phone' => '0235166555', 'email' => '', 'country_iso' => 'NL'],
    ['nr' => 47, 'company' => 'PT.Agung Buana Sentosa', 'address' => 'Komp.Pengampon Square B12,B15,Jl. Semut Baru, Pabean Cantikan', 'zip' => '', 'city' => 'Surabaya', 'phone' => '(031)3550081', 'email' => '', 'country_iso' => 'ID'],
    // Quotation to for OTMPQ-107 — seller is OT-Main; client address unknown → placeholders.
    ['nr' => 48, 'company' => 'MB Melita', 'address' => '-', 'zip' => '-', 'city' => '-', 'phone' => '', 'email' => 's.ibrahim@otmain.com', 'country_iso' => ''],
];
