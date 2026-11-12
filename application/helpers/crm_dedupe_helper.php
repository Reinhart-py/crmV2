<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * One set of duplicate rules for every way a record comes into the CRM:
 * Import Database (Excel / CSV), the check while adding a record by hand, and Lead Finder (Data-Gen).
 *
 * A new record is a duplicate of an existing one when it has
 *   - the same phone number in Contact Number, Contact 2, Alternative Contact, Mobile or Alternative
 *     Mobile (a cell may hold several numbers; +971 / 00971 / 0 prefixes do not matter), or
 *   - the same email address, or
 *   - the same company name, ignoring capitals, spaces, punctuation and words such as
 *     LLC, L.L.C., FZE, FZCO, Trading, Est., Co., Ltd. ("ABC Trading LLC" = "ABC L.L.C.").
 * Lead Finder also compares website, map listing, address and location and shows weaker matches
 * as "Possible duplicate"; these three rules always make a Duplicate there too.
 */

if (!function_exists('crm_dup_name_noise')) {
	function crm_dup_name_noise()
	{
		return array('the', 'llc', 'l', 'c', 'fze', 'fzco', 'fzc', 'fz', 'fzllc', 'est', 'establishment', 'co', 'company', 'ltd', 'limited', 'trading', 'tr', 'pvt', 'private', 'inc', 'branch', 'dmcc', 'spc', 'wll', 'plc', 'gmbh', 'sole', 'proprietorship', 'opc', 'llp', 'and', 'of');
	}
}

if (!function_exists('crm_dup_name_core')) {
	/** Company name reduced to the part that tells companies apart ('' when too short to compare). */
	function crm_dup_name_core($s)
	{
		$s = strtolower(html_entity_decode((string)$s, ENT_QUOTES, 'UTF-8'));
		$s = ltrim($s, "'");
		$s = str_replace('&', ' and ', $s);
		$s = preg_replace('/l\.\s*l\.\s*c\.?/', ' llc ', $s);
		$words = preg_split('/[^a-z0-9\x{0600}-\x{06FF}]+/u', $s, -1, PREG_SPLIT_NO_EMPTY);
		$full = implode('', $words);
		$noise = crm_dup_name_noise();
		$keep = array();
		foreach ($words as $w) if (!in_array($w, $noise, true)) $keep[] = $w;
		$core = implode('', $keep);
		$out = mb_strlen($core) >= 3 ? $core : $full;
		return mb_strlen($out) >= 3 ? $out : '';
	}
}

if (!function_exists('crm_dup_phone_key')) {
	/**
	 * One phone number as a comparable key ('' when it has fewer than 7 digits): digits only, without
	 * leading zeros and without the UAE code 971, then the last 9 digits. So "+971 4 289 6166",
	 * "00971 42896166" and "04 289 6166" are the same, as are "+971 50 123 4567" and "050 123 4567".
	 */
	function crm_dup_phone_key($s)
	{
		$d = ltrim(preg_replace('/\D+/', '', (string)$s), '0');
		if (strlen($d) >= 11 && substr($d, 0, 3) === '971') $d = ltrim(substr($d, 3), '0');
		return strlen($d) >= 7 ? substr($d, -9) : '';
	}
}

if (!function_exists('crm_dup_phone_keys')) {
	/** Phone keys of every number in a cell ("050 123 4567, 04 123 4567" gives two). */
	function crm_dup_phone_keys($s)
	{
		$out = array();
		foreach (preg_split('/[,;\/|]+|\s{2,}/', (string)$s) as $p) {
			$k = crm_dup_phone_key($p);
			if ($k !== '') $out[$k] = true;
		}
		return array_keys($out);
	}
}

if (!function_exists('crm_dup_emails')) {
	/** Every email address in a cell, in lower case. */
	function crm_dup_emails($s)
	{
		$out = array();
		if (preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', (string)$s, $m)) foreach ($m[0] as $e) $out[strtolower($e)] = true;
		return array_keys($out);
	}
}

if (!function_exists('crm_dup_phone_columns')) {
	/** The record columns that hold phone numbers. */
	function crm_dup_phone_columns()
	{
		return array('contact1', 'contact2', 'alt_contact', 'mobile', 'alt_mobile');
	}
}

if (!function_exists('crm_dup_index')) {
	/**
	 * Which existing records have the given keys: array('phone' => [key => record id], 'company' => ..., 'email' => ...).
	 * $want: the same shape with [key => true] for the keys being checked; only those are kept, so memory stays small.
	 * The first (oldest) record wins. The database is read in slices so large databases are fine.
	 */
	function crm_dup_find($want)
	{
		$ci =& get_instance();
		$hit = array('phone' => array(), 'company' => array(), 'email' => array());
		if (empty($want['phone']) && empty($want['company']) && empty($want['email'])) return $hit;
		$last = 0;
		do {
			$rows = $ci->db->query('SELECT id, customer, email, ' . implode(', ', crm_dup_phone_columns()) . ' FROM ci_leads WHERE id > ? ORDER BY id LIMIT 20000', array($last))->result_array();
			foreach ($rows as $r) {
				$id = $last = (int)$r['id'];
				if (!empty($want['phone'])) foreach (crm_dup_phone_columns() as $c) foreach (crm_dup_phone_keys($r[$c]) as $k) if (isset($want['phone'][$k]) && !isset($hit['phone'][$k])) $hit['phone'][$k] = $id;
				if (!empty($want['company'])) { $core = crm_dup_name_core($r['customer']); if ($core !== '' && isset($want['company'][$core]) && !isset($hit['company'][$core])) $hit['company'][$core] = $id; }
				if (!empty($want['email'])) foreach (crm_dup_emails($r['email']) as $e) if (isset($want['email'][$e]) && !isset($hit['email'][$e])) $hit['email'][$e] = $id;
			}
		} while (count($rows) === 20000);
		return $hit;
	}
}
