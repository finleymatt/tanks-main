create or replace
PACKAGE BODY      ONESTOP_DATA_REFRESH
AS
l_errorcode 	     number;
l_errormsg	     varchar2(240);
l_description	     varchar2(240);
l_pkg_proc           varchar2(30) := 'ustb.onestop_data_refresh';
PROCEDURE main
IS
BEGIN
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss') || ' ustb.onestop_data_refresh.main begin';
  write_log (l_pkg_proc, l_description);
  execute immediate 'truncate table ustb.onestop_lust_releases1';
 insert into ustb.onestop_lust_releases1
  select si1.subject_item_designation id,
         upper(substr(si1.subject_item_desc,1,50)) old_name,
         si.subject_item_designation facility_id,
         upper(ustb.func_get_nmed_pstb_rem_contact(si1.master_ai_id, si1.int_doc_id,
           si1.subject_item_category_code, si1.subject_item_id)) staff_name,
         upper(ustb.func_get_pstb_fac_resp_party(si1.master_ai_id, si1.int_doc_id,
           si1.subject_item_category_code, si1.subject_item_id)) responsible_party,
         si1.user_last_updt user_modified,
         si1.tmsp_last_updt date_modified
  from tempo.subject_item si, tempo.subj_item_group_members si_gm,
  tempo.subject_item si1
  where si.subject_item_category_code='GPTF'
   and si.subject_item_type_code = 'GPTK'
   and si.int_doc_id=0
   and ustb.func_isnumeric(si.subject_item_designation) = 'TRUE'
   and si_gm.group_id=si.subject_item_id
   and si_gm.group_category_code=si.subject_item_category_code
   and si_gm.int_doc_id=si.int_doc_id
   and si_gm.master_ai_id=si.master_ai_id
   and si_gm.member_category_code='ACT'
   and si1.subject_item_id=si_gm.member_id
   and si1.int_doc_id=si_gm.int_doc_id
   and si1.master_ai_id=si_gm.master_ai_id
   and si1.subject_item_category_code=si_gm.member_category_code
   and si1.subject_item_type_code = 'ACCR'
   and ustb.func_isnumeric(si1.subject_item_designation) = 'TRUE';

   l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
             || ' onestop_lust_releases1 records inserted '
             || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);

  execute immediate 'truncate table ustb.onestop_lust_status1';
  insert into ustb.onestop_lust_status1
  select si3.subject_item_designation rel_id,
   decode(sia.attribute_desc_code,
   'L83', 'SWQB',
   'L84', 'I-RP',
   'L85', 'I-LT',
   'L86', 'I-SL',
   'L87', 'I-F',
   'L88', 'AC-LT',
   'L89', 'AC-SL',
   'L90', 'AC-RP',
   'L91', 'AC-F',
   'L92', 'C-LT',
   'L93', 'C-SL',
   'L94', 'C-RP',
   'L95', 'C-F',
   'L96', 'HWB',
   'L97', 'M-SL',
   'L98', 'M-SL',
   'L99', 'M-RP',
   'M00', 'M-F',
   'M01', 'GWQB',
   'M02', 'OCD',
   'M03', 'PI-C',
   'M04', 'EPA',
   'M05', 'NFA',
   'M22', 'NFA-S',
   'M23', 'PI-S',
    null) old_status_code,
   sia.attribute_desc_code status_code,
   mtb_ad.attribute_desc_desc status_desc,
   sia.attribute_date date_created,
   substr(sia.attribute_comments_desc,1,240) comments
   from tempo.subject_item si3,
   tempo.subj_item_attribute sia,
   tempo.mtb_attribute_desc mtb_ad
   where si3.subject_item_type_code='ACCR'
    and si3.subject_item_category_code='ACT'
    and si3.int_doc_id = 0
    and ustb.func_isnumeric(si3.subject_item_designation) = 'TRUE'
    and sia.master_ai_id=si3.master_ai_id
    and sia.int_doc_id=si3.int_doc_id
    and sia.subject_item_category_code=si3.subject_item_category_code
    and sia.subject_item_id=si3.subject_item_id
    and sia.attribute_code='F08'
    and sia.sub_attribute_code='A01'
    and sia.attribute_desc_code = mtb_ad.attribute_desc_code;
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
             || ' onestop_lust_status1 records inserted '
             || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);

  execute immediate 'truncate table ustb.onestop_facilities1';
  insert into ustb.onestop_facilities1
  select si.subject_item_designation id,
         si.master_ai_id ai_id,
         upper(substr(si.subject_item_desc,1,50)) facility_name,
         upper(substr(sil.physical_address_line_1,1,50)) address1,
         upper(substr(sil.physical_address_line_2,1,50)) address2,
         upper(substr(sil.physical_address_municipality,1,50)) city,
         sil.physical_address_state_code state,
         sil.physical_address_zip zip,
         owner_id.owner_id,
         silc.on_nal_flag indian,
         si.user_last_updt user_modified,
         si.tmsp_last_updt date_modified
  from tempo.subject_item si, tempo.subj_item_location sil, tempo.subj_item_loc_cultural silc,
  (select distinct master_ai_id, int_doc_id, alternate_ai_name,
  to_number(trim(agency_interest_alt.alternate_ai_id)) owner_id
   from tempo.agency_interest_alt
   where agency_interest_alt.user_group_id='084'
   and ustb.func_isnumeric(agency_interest_alt.alternate_ai_id) = 'TRUE'
   and end_date is null) owner_id
  where si.subject_item_type_code='GPTK'
   and si.subject_item_category_code='GPTF'
   and si.int_doc_id = 0
   and ustb.func_isnumeric(si.subject_item_designation) = 'TRUE'
   and sil.subject_item_id(+)=si.subject_item_id
   and sil.subject_item_category_code(+)=si.subject_item_category_code
   and sil.int_doc_id(+)=si.int_doc_id
   and sil.master_ai_id(+)=si.master_ai_id
   and silc.subject_item_id(+)=si.subject_item_id
   and silc.subject_item_category_code(+)=si.subject_item_category_code
   and silc.int_doc_id(+)=si.int_doc_id
   and silc.master_ai_id(+)=si.master_ai_id
   and owner_id.int_doc_id = si.int_doc_id
   and owner_id.master_ai_id = si.master_ai_id
   and upper(owner_id.alternate_ai_name) = upper(si.subject_item_desc);
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
             || ' onestop_facilities1 records inserted '
             || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);

 execute immediate 'truncate table ustb.onestop_ust_locations1';
 insert into onestop_ust_locations1
 select to_number(trim(si.subject_item_designation)) id,
        si.master_ai_id ai_id,
        upper(substr(si.subject_item_desc,1,50)) name,
        upper(substr(si_l.physical_address_line_1,1,50)) address1,
        upper(substr(si_l.physical_address_line_2,1,50)) address2,
        upper(substr(si_l.physical_address_municipality,1,50)) city,
        si_l.physical_address_state_code state,
        si_l.physical_address_zip zip,
        upper(substr(m_pc.parish_or_county_desc,1,25)) county,
        ltrim(si_l.region_code,'0') district,
        si_ll.latitude_degrees lat_degrees,
        si_ll.latitude_minutes lat_minutes,
        si_ll.latitude_seconds lat_seconds,
        si_ll.longitude_degrees lon_degrees,
        si_ll.longitude_minutes lon_minutes,
        si_ll.longitude_seconds lon_seconds,
        si_ll.latitude_dec_degrees lat_degrees_decimal,
        si_ll.longitude_dec_degrees lon_degrees_decimal,
        si_ll.collected_date ll_date,
        m_cs.coordinate_system_desc ll_method,
        m_rp.ref_point_desc ll_description,
        m_co.coordinate_org_desc ll_datum,
        si_ll.accuracy_desc ll_accuracy,
        si.user_last_updt user_modified,
        si.tmsp_last_updt date_modified
 from tempo.subject_item si,
 tempo.subj_item_loc_lat_long si_ll, tempo.subj_item_location si_l,
 tempo.subj_item_loc_gov_within si_lgw, tempo.mtb_parish_county m_pc,
 tempo.mtb_coord_org m_co, tempo.mtb_reference_point m_rp,
 tempo.mtb_geom_type m_gt, tempo.mtb_coord_system m_cs
 where (si.subject_item_type_code='GPTK'
  and si.subject_item_category_code='GPTF'
  and si.int_doc_id=0)
  and ustb.func_isnumeric(si.subject_item_designation) = 'TRUE'
  and ((si_ll.int_doc_id(+)=si_l.int_doc_id
  and si_ll.subject_item_id(+)=si_l.subject_item_id
  and si_ll.subject_item_category_code(+)=si_l.subject_item_category_code
  and si_ll.master_ai_id(+)=si_l.master_ai_id)
  and (si_l.subject_item_id(+)=si.subject_item_id
  and si_l.subject_item_category_code(+)=si.subject_item_category_code
  and si_l.int_doc_id(+)=si.int_doc_id
  and si_l.master_ai_id(+)=si.master_ai_id)
  and (si_lgw.subject_item_id(+)=si.subject_item_id
  and si_lgw.subject_item_category_code(+)= si.subject_item_category_code
  and si_lgw.int_doc_id(+)=si.int_doc_id
  and si_lgw.master_ai_id(+)=si.master_ai_id)
  and (si_lgw.parish_or_county_code=m_pc.parish_or_county_code (+)))
  and (si_ll.geom_type_code = m_gt.geom_type_code(+))
  and (si_ll.method_code = m_cs.coordinate_system_code(+))
  and (si_ll.datum_code = m_co.coordinate_org_code(+))
  and (si_ll.reference_point_code = m_rp.ref_point_code(+));
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
             || ' onestop_ust_locations1 records inserted '
             || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);

 execute immediate 'truncate table ustb.onestop_owners1';
 insert into ustb.onestop_owners1
 select to_number(trim(oalt.alternate_org_id)) id,
        to_number(null) per_id,
        oalt.master_org_id org_id,
        substr(upper(oalt.alternate_org_name),1,50) owner_name,
        substr(upper(oa.mailing_address_line_1),1,50) address1,
        substr(upper(oa.mailing_address_line_2),1,50) address2,
        substr(upper(oa.mailing_address_municipality),1,25) city,
        oa.mailing_address_state_code state,
        substr(oa.mailing_address_zip,1,5) zip,
        substr(ot.address_or_phone,1,15) phone_number,
        oalt.start_date date_created,
        upper(oalt.user_last_updt) user_modified,
        oalt.tmsp_last_updt date_modified
 from tempo.organization_alt oalt, tempo.organization_address oa,
     tempo.organization_telecom ot
 where oalt.int_doc_id = 0
  and oalt.user_group_id = '084'
  and oalt.end_date is null
  and ustb.func_isnumeric(oalt.alternate_org_id) = 'TRUE'
  and oa.master_org_id(+)=oalt.master_org_id
  and oa.int_doc_id(+)=oalt.int_doc_id
  and ot.master_org_id(+) = oalt.master_org_id
  and ot.int_doc_id(+) = oalt.int_doc_id
  and ot.telecom_type_code(+) = 'WP'
 union
 select to_number(trim(palt.alternate_person_id)) id,
        palt.master_person_id per_id,
        to_number(null) org_id,
        substr(upper(palt.alternate_person_last_name||' '||
        decode(palt.alternate_person_first_name,null,null,palt.alternate_person_first_name)||
        ' '||decode(palt.alternate_person_mid_initial,null,null,
        palt.alternate_person_mid_initial)),1,50) owner_name,
        substr(upper(pa.mailing_address_line_1),1,50) address1,
        substr(upper(pa.mailing_address_line_2),1,50) address2,
        substr(upper(pa.mailing_address_municipality),1,25) city,
        pa.mailing_address_state_code state,
        substr(pa.mailing_address_zip,1,5) zip,
        substr(pt.address_or_phone,1,15) phone_number,
        palt.start_date date_created,
        upper(palt.user_last_updt) user_modified,
        palt.tmsp_last_updt date_modified
 from  tempo.person_alt palt, tempo.person_address pa,
   tempo.person_telecom pt
 where palt.int_doc_id = 0
  and palt.user_group_id = '084'
  and palt.end_date is null
  and ustb.func_isnumeric(palt.alternate_person_id) = 'TRUE'
  and pa.master_person_id(+)=palt.master_person_id
  and pa.int_doc_id(+)=palt.int_doc_id
  and pt.master_person_id(+) = palt.master_person_id
  and pt.int_doc_id(+) = palt.int_doc_id
  and pt.telecom_type_code(+) = 'WP';
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
             || ' onestop_owners1 records inserted '
             || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);

 execute immediate 'truncate table ustb.onestop_operators1';
 insert into ustb.onestop_operators1
 select 'O' || to_char(oalt.master_org_id) id,
        substr(upper(oalt.alternate_org_name),1,50) operator_name,
        substr(upper(oa.mailing_address_line_1),1,50) address1,
        substr(upper(oa.mailing_address_line_2),1,50) address2,
        substr(upper(oa.mailing_address_municipality),1,25) city,
        oa.mailing_address_state_code state,
        substr(oa.mailing_address_zip,1,5) zip,
        substr(ot.address_or_phone,1,15) phone_number,
        oalt.start_date date_created,
        upper(oalt.user_last_updt) user_modified,
        oalt.tmsp_last_updt date_modified
 from tempo.organization_alt oalt, tempo.organization_address oa,
     tempo.organization_telecom ot
 where oalt.int_doc_id = 0
  and oalt.user_group_id = '100'
  and oalt.end_date is null
  and oa.master_org_id(+)=oalt.master_org_id
  and oa.int_doc_id(+)=oalt.int_doc_id
  and ot.master_org_id(+) = oalt.master_org_id
  and ot.int_doc_id(+) = oalt.int_doc_id
  and ot.telecom_type_code(+) = 'WP'
 union
 select 'P' || to_char(palt.master_person_id) id,
        substr(upper(palt.alternate_person_last_name||' '||
        decode(palt.alternate_person_first_name,null,null,palt.alternate_person_first_name)||
        ' '||decode(palt.alternate_person_mid_initial,null,null,
        palt.alternate_person_mid_initial)),1,50) operator_name,
        substr(upper(pa.mailing_address_line_1),1,50) address1,
        substr(upper(pa.mailing_address_line_2),1,50) address2,
        substr(upper(pa.mailing_address_municipality),1,25) city,
        pa.mailing_address_state_code state,
        substr(pa.mailing_address_zip,1,5) zip,
        substr(pt.address_or_phone,1,15) phone_number,
        palt.start_date date_created,
        upper(palt.user_last_updt) user_modified,
        palt.tmsp_last_updt date_modified
 from tempo.person_alt palt, tempo.person_address pa,
   tempo.person_telecom pt
 where palt.int_doc_id = 0
  and palt.user_group_id = '100'
  and palt.end_date is null
  and pa.master_person_id(+) = palt.master_person_id
  and pa.int_doc_id(+)=palt.int_doc_id
  and pt.master_person_id(+) = palt.master_person_id
  and pt.int_doc_id(+) = palt.int_doc_id
  and pt.telecom_type_code(+) = 'WP';
  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss')
             || ' onestop_operators1 records inserted '
             || to_char(SQL%ROWCOUNT);
  write_log (l_pkg_proc, l_description);


  ustx.mview_refresh.main;

  l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss') || ' ustb.onestop_mview_refresh.main end';
  write_log (l_pkg_proc, l_description);
  EXCEPTION
    WHEN OTHERS then
      l_description := to_char(sysdate, 'yyyy-mm-dd hh24:mi:ss') || ' ustb.onestop_data_refresh.main error';
      write_log (l_pkg_proc, l_description);
END main;
PROCEDURE write_log (
  p_pkg_proc               in   varchar2,
  p_description            in   varchar2)
IS
BEGIN
   l_ErrorCode := SQLCODE;
   l_ErrorMsg  := SUBSTR(SQLERRM,1,240);
   insert into ustb.onestop_log_table (pkg_proc, code, message, description)
   values (p_pkg_proc, l_errorcode, l_errormsg, p_description);
   commit;
END write_log;
END;
