<?php declare(strict_types=1);
//============================================================+
// File name   : qrcode.php
// Version     : 1.0.010
// Begin       : 2010-03-22
// Last Update : 2012-07-25
// Author      : Nicola Asuni - Tecnick.com LTD - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2010-2012 Nicola Asuni - Tecnick.com LTD
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
// -------------------------------------------------------------------
//
// DESCRIPTION :
//
// Class to create QR-code arrays for TCPDF class.
// QR Code symbol is a 2D barcode that can be scanned by
// handy terminals such as a mobile phone with CCD.
// The capacity of QR Code is up to 7000 digits or 4000
// characters, and has high robustness.
// This class supports QR Code model 2, described in
// JIS (Japanese Industrial Standards) X0510:2004
// or ISO/IEC 18004.
// Currently the following features are not supported:
// ECI and FNC1 mode, Micro QR Code, QR Code model 1,
// Structured mode.
//
// This class is derived from the following projects:
// ---------------------------------------------------------
// "PHP QR Code encoder"
// License: GNU-LGPLv3
// Copyright (C) 2010 by Dominik Dzienia <deltalab at poczta dot fm>
// http://phpqrcode.sourceforge.net/
// https://sourceforge.net/projects/phpqrcode/
//
// The "PHP QR Code encoder" is based on
// "C libqrencode library" (ver. 3.1.1)
// License: GNU-LGPL 2.1
// Copyright (C) 2006-2010 by Kentaro Fukuchi
// http://megaui.net/fukuchi/works/qrencode/index.en.html
//
// Reed-Solomon code encoder is written by Phil Karn, KA9Q.
// Copyright (C) 2002-2006 Phil Karn, KA9Q
//
// QR Code is registered trademark of DENSO WAVE INCORPORATED
// http://www.denso-wave.com/qrcode/index-e.html
// ---------------------------------------------------------
//============================================================+

/**
 * @file
 * Class to create QR-code arrays for TCPDF class.
 * QR Code symbol is a 2D barcode that can be scanned by handy terminals such as a mobile phone with CCD.
 * The capacity of QR Code is up to 7000 digits or 4000 characters, and has high robustness.
 * This class supports QR Code model 2, described in JIS (Japanese Industrial Standards) X0510:2004 or ISO/IEC 18004.
 * Currently the following features are not supported: ECI and FNC1 mode, Micro QR Code, QR Code model 1, Structured mode.
 *
 * This class is derived from "PHP QR Code encoder" by Dominik Dzienia (http://phpqrcode.sourceforge.net/) based on "libqrencode C library 3.1.1." by Kentaro Fukuchi (http://megaui.net/fukuchi/works/qrencode/index.en.html), contains Reed-Solomon code written by Phil Karn, KA9Q. QR Code is registered trademark of DENSO WAVE INCORPORATED (http://www.denso-wave.com/qrcode/index-e.html).
 * Please read comments on this class source file for full copyright and license information.
 *
 * @package com.tecnick.tcpdf
 * @author Nicola Asuni
 * @version 1.0.010
 */

// definitions
if (!\defined('QRCODEDEFS')) {
	/**
	 * Indicate that definitions for this class are set
	 */
	\define('QRCODEDEFS', true);

	// -----------------------------------------------------

	// Encoding modes (characters which can be encoded in QRcode)

	/**
	 * Encoding mode
	 */
	\define('QR_MODE_NL', -1);

	/**
	 * Encoding mode numeric (0-9). 3 characters are encoded to 10bit length. In theory, 7089 characters or less can be stored in a QRcode.
	 */
	\define('QR_MODE_NM', 0);

	/**
	 * Encoding mode alphanumeric (0-9A-Z $%*+-./:) 45characters. 2 characters are encoded to 11bit length. In theory, 4296 characters or less can be stored in a QRcode.
	 */
	\define('QR_MODE_AN', 1);

	/**
	 * Encoding mode 8bit byte data. In theory, 2953 characters or less can be stored in a QRcode.
	 */
	\define('QR_MODE_8B', 2);

	/**
	 * Encoding mode KANJI. A KANJI character (multibyte character) is encoded to 13bit length. In theory, 1817 characters or less can be stored in a QRcode.
	 */
	\define('QR_MODE_KJ', 3);

	/**
	 * Encoding mode STRUCTURED (currently unsupported)
	 */
	\define('QR_MODE_ST', 4);

	// -----------------------------------------------------

	// Levels of error correction.
	// QRcode has a function of an error correcting for miss reading that white is black.
	// Error correcting is defined in 4 level as below.

	/**
	 * Error correction level L : About 7% or less errors can be corrected.
	 */
	\define('QR_ECLEVEL_L', 0);

	/**
	 * Error correction level M : About 15% or less errors can be corrected.
	 */
	\define('QR_ECLEVEL_M', 1);

	/**
	 * Error correction level Q : About 25% or less errors can be corrected.
	 */
	\define('QR_ECLEVEL_Q', 2);

	/**
	 * Error correction level H : About 30% or less errors can be corrected.
	 */
	\define('QR_ECLEVEL_H', 3);

	// -----------------------------------------------------

	// Version. Size of QRcode is defined as version.
	// Version is from 1 to 40.
	// Version 1 is 21*21 matrix. And 4 modules increases whenever 1 version increases.
	// So version 40 is 177*177 matrix.

	/**
	 * Maximum QR Code version.
	 */
	\define('QRSPEC_VERSION_MAX', 40);

    /**
     * Maximum matrix size for maximum version (version 40 is 177*177 matrix).
     */
    \define('QRSPEC_WIDTH_MAX', 177);

	// -----------------------------------------------------

    /**
     * Matrix index to get width from $capacity array.
     */
    \define('QRCAP_WIDTH',    0);

    /**
     * Matrix index to get number of words from $capacity array.
     */
    \define('QRCAP_WORDS',    1);

    /**
     * Matrix index to get remainder from $capacity array.
     */
    \define('QRCAP_REMINDER', 2);

    /**
     * Matrix index to get error correction level from $capacity array.
     */
    \define('QRCAP_EC',       3);

	// -----------------------------------------------------

	// Structure (currently usupported)

    /**
     * Number of header bits for structured mode
     */
    \define('STRUCTURE_HEADER_BITS',  20);

    /**
     * Max number of symbols for structured mode
     */
    \define('MAX_STRUCTURED_SYMBOLS', 16);

	// -----------------------------------------------------

    // Masks

    /**
     * Down point base value for case 1 mask pattern (concatenation of same color in a line or a column)
     */
    \define('N1',  3);

	/**
	 * Down point base value for case 2 mask pattern (module block of same color)
	 */
	\define('N2',  3);

	/**
	 * Down point base value for case 3 mask pattern (1:1:3:1:1(dark:bright:dark:bright:dark)pattern in a line or a column)
	 */
	\define('N3', 40);

	/**
	 * Down point base value for case 4 mask pattern (ration of dark modules in whole)
	 */
	\define('N4', 10);

	// -----------------------------------------------------

	// Optimization settings

	/**
	 * if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
	 */
	\define('QR_FIND_BEST_MASK', true);

	/**
	 * if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
	 */
	\define('QR_FIND_FROM_RANDOM', 2);

	/**
	 * when QR_FIND_BEST_MASK === false
	 */
	\define('QR_DEFAULT_MASK', 2);

	// -----------------------------------------------------
} // end of definitions

/**
 * @class QRcode
 * Class to create QR-code arrays for TCPDF class.
 * QR Code symbol is a 2D barcode that can be scanned by handy terminals such as a mobile phone with CCD.
 * The capacity of QR Code is up to 7000 digits or 4000 characters, and has high robustness.
 * This class supports QR Code model 2, described in JIS (Japanese Industrial Standards) X0510:2004 or ISO/IEC 18004.
 * Currently the following features are not supported: ECI and FNC1 mode, Micro QR Code, QR Code model 1, Structured mode.
 *
 * This class is derived from "PHP QR Code encoder" by Dominik Dzienia (http://phpqrcode.sourceforge.net/) based on "libqrencode C library 3.1.1." by Kentaro Fukuchi (http://megaui.net/fukuchi/works/qrencode/index.en.html), contains Reed-Solomon code written by Phil Karn, KA9Q. QR Code is registered trademark of DENSO WAVE INCORPORATED (http://www.denso-wave.com/qrcode/index-e.html).
 * Please read comments on this class source file for full copyright and license information.
 *
 * @package com.tecnick.tcpdf
 * @author Nicola Asuni
 * @version 1.0.010
 */
class QRcode {
	/**
	 * Barcode array to be returned which is readable by TCPDF.
	 * @protected
	 */
	protected $barcode_array = [];

	/**
	 * QR code version. Size of QRcode is defined as version. Version is from 1 to 40. Version 1 is 21*21 matrix. And 4 modules increases whenever 1 version increases. So version 40 is 177*177 matrix.
	 * @protected
	 */
	protected $version = 0;

	/**
	 * Levels of error correction. See definitions for possible values.
	 * @protected
	 */
	protected $level = QR_ECLEVEL_L;

	/**
	 * Encoding mode.
	 * @protected
	 */
	protected $hint = QR_MODE_8B;

	/**
	 * Boolean flag, if true the input string will be converted to uppercase.
	 * @protected
	 */
	protected $casesensitive = true;

	/**
	 * Structured QR code (not supported yet).
	 * @protected
	 */
	protected $structured = 0;

	/**
	 * Mask data.
	 * @protected
	 */
	protected $data;

	// FrameFiller

	/**
	 * Width.
	 * @protected
	 */
	protected $width;

	/**
	 * Frame.
	 * @protected
	 */
	protected $frame;

	/**
	 * X position of bit.
	 * @protected
	 */
	protected $x;

	/**
	 * Y position of bit.
	 * @protected
	 */
	protected $y;

	/**
	 * Direction.
	 * @protected
	 */
	protected $dir;

	/**
	 * Single bit value.
	 * @protected
	 */
	protected $bit;

	// ---- QRrawcode ----

	/**
	 * Data code.
	 * @protected
	 */
	protected $datacode = [];

	/**
	 * Error correction code.
	 * @protected
	 */
	protected $ecccode = [];

	/**
	 * Blocks.
	 * @protected
	 */
	protected $blocks;

	/**
	 * Reed-Solomon blocks.
	 * @protected
	 */
	protected $rsblocks = []; //of RSblock

	/**
	 * Counter.
	 * @protected
	 */
	protected $count;

	/**
	 * Data length.
	 * @protected
	 */
	protected $dataLength;

	/**
	 * Error correction length.
	 * @protected
	 */
	protected $eccLength;

	/**
	 * Value b1.
	 * @protected
	 */
	protected $b1;

	// ---- QRmask ----

	/**
	 * Run length.
	 * @protected
	 */
	protected $runLength = [];

	// ---- QRsplit ----

	/**
	 * Input data string.
	 * @protected
	 */
	protected $dataStr = '';

	/**
	 * Input items.
	 * @protected
	 */
	protected $items;

	// Reed-Solomon items

	/**
	 * Reed-Solomon items.
	 * @protected
	 */
	protected $rsitems = [];

	/**
	 * Array of frames.
	 * @protected
	 */
	protected $frames = [];

	/**
	 * Alphabet-numeric convesion table.
	 * @protected
	 */
	protected $anTable = [
		-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
		-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
		36, -1, -1, -1, 37, 38, -1, -1, -1, -1, 39, 40, -1, 41, 42, 43,
		 0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 44, -1, -1, -1, -1, -1,
		-1, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24,
		25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, -1, -1, -1, -1, -1,
		-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
		-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
		];

	/**
	 * Array Table of the capacity of symbols.
	 * See Table 1 (pp.13) and Table 12-16 (pp.30-36), JIS X0510:2004.
	 * @protected
	 */
	protected $capacity = [
		[0,    0, 0, [0,    0,    0,    0]],
		[21,   26, 0, [7,   10,   13,   17]], //  1
		[25,   44, 7, [10,   16,   22,   28]],
		[29,   70, 7, [15,   26,   36,   44]],
		[33,  100, 7, [20,   36,   52,   64]],
		[37,  134, 7, [26,   48,   72,   88]], //  5
		[41,  172, 7, [36,   64,   96,  112]],
		[45,  196, 0, [40,   72,  108,  130]],
		[49,  242, 0, [48,   88,  132,  156]],
		[53,  292, 0, [60,  110,  160,  192]],
		[57,  346, 0, [72,  130,  192,  224]], // 10
		[61,  404, 0, [80,  150,  224,  264]],
		[65,  466, 0, [96,  176,  260,  308]],
		[69,  532, 0, [104,  198,  288,  352]],
		[73,  581, 3, [120,  216,  320,  384]],
		[77,  655, 3, [132,  240,  360,  432]], // 15
		[81,  733, 3, [144,  280,  408,  480]],
		[85,  815, 3, [168,  308,  448,  532]],
		[89,  901, 3, [180,  338,  504,  588]],
		[93,  991, 3, [196,  364,  546,  650]],
		[97, 1085, 3, [224,  416,  600,  700]], // 20
		[101, 1156, 4, [224,  442,  644,  750]],
		[105, 1258, 4, [252,  476,  690,  816]],
		[109, 1364, 4, [270,  504,  750,  900]],
		[113, 1474, 4, [300,  560,  810,  960]],
		[117, 1588, 4, [312,  588,  870, 1050]], // 25
		[121, 1706, 4, [336,  644,  952, 1110]],
		[125, 1828, 4, [360,  700, 1020, 1200]],
		[129, 1921, 3, [390,  728, 1050, 1260]],
		[133, 2051, 3, [420,  784, 1140, 1350]],
		[137, 2185, 3, [450,  812, 1200, 1440]], // 30
		[141, 2323, 3, [480,  868, 1290, 1530]],
		[145, 2465, 3, [510,  924, 1350, 1620]],
		[149, 2611, 3, [540,  980, 1440, 1710]],
		[153, 2761, 3, [570, 1036, 1530, 1800]],
		[157, 2876, 0, [570, 1064, 1590, 1890]], // 35
		[161, 3034, 0, [600, 1120, 1680, 1980]],
		[165, 3196, 0, [630, 1204, 1770, 2100]],
		[169, 3362, 0, [660, 1260, 1860, 2220]],
		[173, 3532, 0, [720, 1316, 1950, 2310]],
		[177, 3706, 0, [750, 1372, 2040, 2430]],  // 40
	];

	/**
	 * Array Length indicator.
	 * @protected
	 */
	protected $lengthTableBits = [
		[10, 12, 14],
		[9, 11, 13],
		[8, 16, 16],
		[8, 10, 12],
	];

	/**
	 * Array Table of the error correction code (Reed-Solomon block).
	 * See Table 12-16 (pp.30-36), JIS X0510:2004.
	 * @protected
	 */
	protected $eccTable = [
		[[0,  0], [0,  0], [0,  0], [0,  0]],
		[[1,  0], [1,  0], [1,  0], [1,  0]], //  1
		[[1,  0], [1,  0], [1,  0], [1,  0]],
		[[1,  0], [1,  0], [2,  0], [2,  0]],
		[[1,  0], [2,  0], [2,  0], [4,  0]],
		[[1,  0], [2,  0], [2,  2], [2,  2]], //  5
		[[2,  0], [4,  0], [4,  0], [4,  0]],
		[[2,  0], [4,  0], [2,  4], [4,  1]],
		[[2,  0], [2,  2], [4,  2], [4,  2]],
		[[2,  0], [3,  2], [4,  4], [4,  4]],
		[[2,  2], [4,  1], [6,  2], [6,  2]], // 10
		[[4,  0], [1,  4], [4,  4], [3,  8]],
		[[2,  2], [6,  2], [4,  6], [7,  4]],
		[[4,  0], [8,  1], [8,  4], [12,  4]],
		[[3,  1], [4,  5], [11,  5], [11,  5]],
		[[5,  1], [5,  5], [5,  7], [11,  7]], // 15
		[[5,  1], [7,  3], [15,  2], [3, 13]],
		[[1,  5], [10,  1], [1, 15], [2, 17]],
		[[5,  1], [9,  4], [17,  1], [2, 19]],
		[[3,  4], [3, 11], [17,  4], [9, 16]],
		[[3,  5], [3, 13], [15,  5], [15, 10]], // 20
		[[4,  4], [17,  0], [17,  6], [19,  6]],
		[[2,  7], [17,  0], [7, 16], [34,  0]],
		[[4,  5], [4, 14], [11, 14], [16, 14]],
		[[6,  4], [6, 14], [11, 16], [30,  2]],
		[[8,  4], [8, 13], [7, 22], [22, 13]], // 25
		[[10,  2], [19,  4], [28,  6], [33,  4]],
		[[8,  4], [22,  3], [8, 26], [12, 28]],
		[[3, 10], [3, 23], [4, 31], [11, 31]],
		[[7,  7], [21,  7], [1, 37], [19, 26]],
		[[5, 10], [19, 10], [15, 25], [23, 25]], // 30
		[[13,  3], [2, 29], [42,  1], [23, 28]],
		[[17,  0], [10, 23], [10, 35], [19, 35]],
		[[17,  1], [14, 21], [29, 19], [11, 46]],
		[[13,  6], [14, 23], [44,  7], [59,  1]],
		[[12,  7], [12, 26], [39, 14], [22, 41]], // 35
		[[6, 14], [6, 34], [46, 10], [2, 64]],
		[[17,  4], [29, 14], [49, 10], [24, 46]],
		[[4, 18], [13, 32], [48, 14], [42, 32]],
		[[20,  4], [40,  7], [43, 22], [10, 67]],
		[[19,  6], [18, 31], [34, 34], [20, 61]],  // 40
	];

	/**
	 * Array Positions of alignment patterns.
	 * This array includes only the second and the third position of the alignment patterns. Rest of them can be calculated from the distance between them.
	 * See Table 1 in Appendix E (pp.71) of JIS X0510:2004.
	 * @protected
	 */
	protected $alignmentPattern = [
		[0,  0],
		[0,  0], [18,  0], [22,  0], [26,  0], [30,  0], //  1- 5
		[34,  0], [22, 38], [24, 42], [26, 46], [28, 50], //  6-10
		[30, 54], [32, 58], [34, 62], [26, 46], [26, 48], // 11-15
		[26, 50], [30, 54], [30, 56], [30, 58], [34, 62], // 16-20
		[28, 50], [26, 50], [30, 54], [28, 54], [32, 58], // 21-25
		[30, 58], [34, 62], [26, 50], [30, 54], [26, 52], // 26-30
		[30, 56], [34, 60], [30, 58], [34, 62], [30, 54], // 31-35
		[24, 50], [28, 54], [32, 58], [26, 54], [30, 58],  // 35-40
	];

	/**
	 * Array Version information pattern (BCH coded).
	 * See Table 1 in Appendix D (pp.68) of JIS X0510:2004.
	 * size: [QRSPEC_VERSION_MAX - 6]
	 * @protected
	 */
	protected $versionPattern = [
		0x07c94, 0x085bc, 0x09a99, 0x0a4d3, 0x0bbf6, 0x0c762, 0x0d847, 0x0e60d,
		0x0f928, 0x10b78, 0x1145d, 0x12a17, 0x13532, 0x149a6, 0x15683, 0x168c9,
		0x177ec, 0x18ec4, 0x191e1, 0x1afab, 0x1b08e, 0x1cc1a, 0x1d33f, 0x1ed75,
		0x1f250, 0x209d5, 0x216f0, 0x228ba, 0x2379f, 0x24b0b, 0x2542e, 0x26a64,
		0x27541, 0x28c69,
	];

	/**
	 * Array Format information
	 * @protected
	 */
	protected $formatInfo = [
		[0x77c4, 0x72f3, 0x7daa, 0x789d, 0x662f, 0x6318, 0x6c41, 0x6976],
		[0x5412, 0x5125, 0x5e7c, 0x5b4b, 0x45f9, 0x40ce, 0x4f97, 0x4aa0],
		[0x355f, 0x3068, 0x3f31, 0x3a06, 0x24b4, 0x2183, 0x2eda, 0x2bed],
		[0x1689, 0x13be, 0x1ce7, 0x19d0, 0x0762, 0x0255, 0x0d0c, 0x083b],
	];

	// -------------------------------------------------
	// -------------------------------------------------

	/**
	 * This is the class constructor.
	 * Creates a QRcode object
	 * @param string $code    code to represent using QRcode
	 * @param string $eclevel error level: <ul><li>L : About 7% or less errors can be corrected.</li><li>M : About 15% or less errors can be corrected.</li><li>Q : About 25% or less errors can be corrected.</li><li>H : About 30% or less errors can be corrected.</li></ul>
	 * @public
	 * @since 1.0.000
	 */
	public function __construct($code, $eclevel = 'L') {
		$barcode_array = [];
		if (($code === null) || ($code == '\0') || ($code == '')) {
			return false;
		}
		// set error correction level
		$this->level = \array_search($eclevel, ['L', 'M', 'Q', 'H']);
		if ($this->level === false) {
			$this->level = QR_ECLEVEL_L;
		}
		if (($this->hint != QR_MODE_8B) && ($this->hint != QR_MODE_KJ)) {
			return false;
		}
		if (($this->version < 0) || ($this->version > QRSPEC_VERSION_MAX)) {
			return false;
		}
		$this->items = [];
		$this->encodeString($code);
		if ($this->data === null) {
			return false;
		}
		$qrTab                     = $this->binarize($this->data);
		$size                      = \count($qrTab);
		$barcode_array['num_rows'] = $size;
		$barcode_array['num_cols'] = $size;
		$barcode_array['bcode']    = [];
		foreach ($qrTab as $line) {
			$arrAdd = [];
			foreach (\str_split($line) as $char) {
				$arrAdd[] = ($char == '1')?1:0;
			}
			$barcode_array['bcode'][] = $arrAdd;
		}
		$this->barcode_array = $barcode_array;
	}

	/**
	 * Returns a barcode array which is readable by TCPDF
	 * @return array barcode array readable by TCPDF;
	 * @public
	 */
	public function getBarcodeArray() {
		return $this->barcode_array;
	}

	/**
	 * Convert the frame in binary form
	 * @param  array $frame array to binarize
	 * @return array frame in binary form
	 */
	protected function binarize($frame) {
		$len = \count($frame);
		// the frame is square (width = height)
		foreach ($frame as &$frameLine) {
			for ($i = 0; $i < $len; ++$i) {
				$frameLine[$i] = (\ord($frameLine[$i]) & 1)?'1':'0';
			}
		}
		return $frame;
	}

	/**
	 * Encode the input string to QR code
	 * @param string $string input string to encode
	 */
	protected function encodeString($string) {
		$this->dataStr = $string;
		if (!$this->casesensitive) {
			$this->toUpper();
		}
		$ret = $this->splitString();
		if ($ret < 0) {
			return null;
		}
		$this->encodeMask(-1);
	}

	/**
	 * Encode mask
	 * @param int $mask masking mode
	 */
	protected function encodeMask($mask) {
		$spec           = [0, 0, 0, 0, 0];
		$this->datacode = $this->getByteStream($this->items);

		if ($this->datacode === null) {
			return null;
		}
		$spec             = $this->getEccSpec($this->version, $this->level, $spec);
		$this->b1         = $this->rsBlockNum1($spec);
		$this->dataLength = $this->rsDataLength($spec);
		$this->eccLength  = $this->rsEccLength($spec);
		$this->ecccode    = \array_fill(0, $this->eccLength, 0);
		$this->blocks     = $this->rsBlockNum($spec);
		$ret              = $this->init($spec);
		if ($ret < 0) {
			return null;
		}
		$this->count = 0;
		$this->width = $this->getWidth($this->version);
		$this->frame = $this->newFrame($this->version);
		$this->x     = $this->width - 1;
		$this->y     = $this->width - 1;
		$this->dir   = -1;
		$this->bit   = -1;
		// inteleaved data and ecc codes
		for ($i = 0; $i < ($this->dataLength + $this->eccLength); ++$i) {
			$code = $this->getCode();
			$bit  = 0x80;
			for ($j = 0; $j < 8; ++$j) {
				$addr = $this->getNextPosition();
				$this->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
				$bit = $bit >> 1;
			}
		}
		// remainder bits
		$j = $this->getRemainder($this->version);
		for ($i = 0; $i < $j; ++$i) {
			$addr = $this->getNextPosition();
			$this->setFrameAt($addr, 0x02);
		}
		// masking
		$this->runLength = \array_fill(0, QRSPEC_WIDTH_MAX + 1, 0);
		if ($mask < 0) {
			if (QR_FIND_BEST_MASK) {
				$masked = $this->mask($this->width, $this->frame, $this->level);
			} else {
				$masked = $this->makeMask($this->width, $this->frame, ((int) QR_DEFAULT_MASK % 8), $this->level);
			}
		} else {
			$masked = $this->makeMask($this->width, $this->frame, $mask, $this->level);
		}
		if ($masked == null) {
			return null;
		}
		$this->data = $masked;
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// FrameFiller

	/**
	 * Set frame value at specified position
	 * @param array $at  x,y position
	 * @param int   $val value of the character to set
	 */
	protected function setFrameAt($at, $val) : void {
		$this->frame[$at['y']][$at['x']] = \chr($val);
	}

	/**
	 * Get frame value at specified position
	 * @param  array $at x,y position
	 * @return value at specified position
	 */
	protected function getFrameAt($at) {
		return \ord($this->frame[$at['y']][$at['x']]);
	}

	/**
	 * Return the next frame position
	 * @return array of x,y coordinates
	 */
	protected function getNextPosition() {
		do {
			if ($this->bit == -1) {
				$this->bit = 0;
				return ['x' => $this->x, 'y' => $this->y];
			}
			$x = $this->x;
			$y = $this->y;
			$w = $this->width;
			if ($this->bit == 0) {
				--$x;
				++$this->bit;
			} else {
				++$x;
				$y += $this->dir;
				--$this->bit;
			}
			if ($this->dir < 0) {
				if ($y < 0) {
					$y = 0;
					$x -= 2;
					$this->dir = 1;
					if ($x == 6) {
						--$x;
						$y = 9;
					}
				}
			} else {
				if ($y == $w) {
					$y = $w - 1;
					$x -= 2;
					$this->dir = -1;
					if ($x == 6) {
						--$x;
						$y -= 8;
					}
				}
			}
			if (($x < 0) || ($y < 0)) {
				return null;
			}
			$this->x = $x;
			$this->y = $y;
		} while(\ord($this->frame[$y][$x]) & 0x80);
		return ['x' => $x, 'y' => $y];
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRrawcode

	/**
	 * Initialize code.
	 * @param  array $spec array of ECC specification
	 * @return int   0 in case of success, -1 in case of error
	 */
	protected function init($spec) {
		$dl      = $this->rsDataCodes1($spec);
		$el      = $this->rsEccCodes1($spec);
		$rs      = $this->init_rs(8, 0x11d, 0, 1, $el, 255 - $dl - $el);
		$blockNo = 0;
		$dataPos = 0;
		$eccPos  = 0;
		$endfor  = $this->rsBlockNum1($spec);
		for ($i = 0; $i < $endfor; ++$i) {
			$ecc                                    = \array_slice($this->ecccode, $eccPos);
			$this->rsblocks[$blockNo]               = [];
			$this->rsblocks[$blockNo]['dataLength'] = $dl;
			$this->rsblocks[$blockNo]['data']       = \array_slice($this->datacode, $dataPos);
			$this->rsblocks[$blockNo]['eccLength']  = $el;
			$ecc                                    = $this->encode_rs_char($rs, $this->rsblocks[$blockNo]['data'], $ecc);
			$this->rsblocks[$blockNo]['ecc']        = $ecc;
			$this->ecccode                          = \array_merge(\array_slice($this->ecccode,0, $eccPos), $ecc);
			$dataPos += $dl;
			$eccPos  += $el;
			++$blockNo;
		}
		if ($this->rsBlockNum2($spec) == 0) {
			return 0;
		}
		$dl = $this->rsDataCodes2($spec);
		$el = $this->rsEccCodes2($spec);
		$rs = $this->init_rs(8, 0x11d, 0, 1, $el, 255 - $dl - $el);
		if ($rs == null) {
			return -1;
		}
		$endfor = $this->rsBlockNum2($spec);
		for ($i = 0; $i < $endfor; ++$i) {
			$ecc                                    = \array_slice($this->ecccode, $eccPos);
			$this->rsblocks[$blockNo]               = [];
			$this->rsblocks[$blockNo]['dataLength'] = $dl;
			$this->rsblocks[$blockNo]['data']       = \array_slice($this->datacode, $dataPos);
			$this->rsblocks[$blockNo]['eccLength']  = $el;
			$ecc                                    = $this->encode_rs_char($rs, $this->rsblocks[$blockNo]['data'], $ecc);
			$this->rsblocks[$blockNo]['ecc']        = $ecc;
			$this->ecccode                          = \array_merge(\array_slice($this->ecccode, 0, $eccPos), $ecc);
			$dataPos += $dl;
			$eccPos  += $el;
			++$blockNo;
		}
		return 0;
	}

	/**
	 * Return Reed-Solomon block code.
	 * @return array rsblocks
	 */
	protected function getCode() {
		if ($this->count < $this->dataLength) {
			$row = $this->count % $this->blocks;
			$col = $this->count / $this->blocks;
			if ($col >= $this->rsblocks[0]['dataLength']) {
				$row += $this->b1;
			}
			$ret = $this->rsblocks[$row]['data'][$col];
		} elseif ($this->count < $this->dataLength + $this->eccLength) {
			$row = ($this->count - $this->dataLength) % $this->blocks;
			$col = ($this->count - $this->dataLength) / $this->blocks;
			$ret = $this->rsblocks[$row]['ecc'][$col];
		} else {
			return 0;
		}
		++$this->count;
		return $ret;
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRmask

	 /**
	  * Write Format Information on frame and returns the number of black bits
	   * @param  int   $width frame width
	   * @param  array $frame frame
	   * @param  array $mask  masking mode
	   * @param  int   $level error correction level
	   * @return int   blacks
	  */
	 protected function writeFormatInformation($width, &$frame, $mask, $level) {
		$blacks = 0;
		$format = $this->getFormatInfo($mask, $level);
		for ($i = 0; $i < 8; ++$i) {
			if ($format & 1) {
				$blacks += 2;
				$v = 0x85;
			} else {
				$v = 0x84;
			}
			$frame[8][$width - 1 - $i] = \chr($v);
			if ($i < 6) {
				$frame[$i][8] = \chr($v);
			} else {
				$frame[$i + 1][8] = \chr($v);
			}
			$format = $format >> 1;
		}
		for ($i = 0; $i < 7; ++$i) {
		if ($format & 1) {
			$blacks += 2;
			$v = 0x85;
		} else {
			$v = 0x84;
		}
		$frame[$width - 7 + $i][8] = \chr($v);
		if ($i == 0) {
			$frame[8][7] = \chr($v);
		} else {
			$frame[8][6 - $i] = \chr($v);
		}
		$format = $format >> 1;
		}
		return $blacks;
	}

	 /**
	  * mask0
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask0($x, $y) {
		return ($x + $y) & 1;
	}

	 /**
	  * mask1
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask1($x, $y) {
		return ($y & 1);
	}

	 /**
	  * mask2
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask2($x, $y) {
		return ($x % 3);
	}

	 /**
	  * mask3
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask3($x, $y) {
		return ($x + $y) % 3;
	}

	 /**
	  * mask4
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask4($x, $y) {
		return (((int) ($y / 2)) + ((int) ($x / 3))) & 1;
	}

	 /**
	  * mask5
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask5($x, $y) {
		return (($x * $y) & 1) + ($x * $y) % 3;
	}

	 /**
	  * mask6
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask6($x, $y) {
		return ((($x * $y) & 1) + ($x * $y) % 3) & 1;
	}

	 /**
	  * mask7
	   * @param  int $x X position
	   * @param  int $y Y position
	   * @return int mask
	  */
	 protected function mask7($x, $y) {
		return ((($x * $y) % 3) + (($x + $y) & 1)) & 1;
	}

	/**
	 * Return bitmask
	 * @param  int   $maskNo mask number
	 * @param  int   $width  width
	 * @param  array $frame  frame
	 * @return array bitmask
	 */
	protected function generateMaskNo($maskNo, $width, $frame) {
		$bitMask = \array_fill(0, $width, \array_fill(0, $width, 0));
		for ($y = 0; $y < $width; ++$y) {
			for ($x = 0; $x < $width; ++$x) {
				if (\ord($frame[$y][$x]) & 0x80) {
					$bitMask[$y][$x] = 0;
				} else {
					$maskFunc        = \call_user_func([$this, 'mask'.$maskNo], $x, $y);
					$bitMask[$y][$x] = ($maskFunc == 0)?1:0;
				}
			}
		}
		return $bitMask;
	}

	 /**
	  * makeMaskNo
	   * @param  int  $maskNo
	   * @param  int  $width
	   * @param  int  $s
	   * @param  int  $d
	   * @param  bool $maskGenOnly
	   * @return int  b
	  */
	 protected function makeMaskNo($maskNo, $width, $s, &$d, $maskGenOnly = false) {
		$b       = 0;
		$bitMask = [];
		$bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);
		if ($maskGenOnly) {
			return;
		}
		$d = $s;
		for ($y = 0; $y < $width; ++$y) {
			for ($x = 0; $x < $width; ++$x) {
				if ($bitMask[$y][$x] == 1) {
					$d[$y][$x] = \chr(\ord($s[$y][$x]) ^ ((int) ($bitMask[$y][$x])));
				}
				$b += (int) (\ord($d[$y][$x]) & 1);
			}
		}
		return $b;
	}

	 /**
	  * makeMask
	   * @param  int   $width
	   * @param  array $frame
	   * @param  int   $maskNo
	   * @param  int   $level
	   * @return array mask
	  */
	 protected function makeMask($width, $frame, $maskNo, $level) {
		$masked = \array_fill(0, $width, \str_repeat("\0", $width));
		$this->makeMaskNo($maskNo, $width, $frame, $masked);
		$this->writeFormatInformation($width, $masked, $maskNo, $level);
		return $masked;
	}

	 /**
	  * calcN1N3
	   * @param  int $length
	   * @return int demerit
	  */
	 protected function calcN1N3($length) {
		$demerit = 0;
		for ($i = 0; $i < $length; ++$i) {
			if ($this->runLength[$i] >= 5) {
				$demerit += (N1 + ($this->runLength[$i] - 5));
			}
			if ($i & 1) {
				if (($i >= 3) && ($i < ($length - 2)) && ($this->runLength[$i] % 3 == 0)) {
					$fact = (int) ($this->runLength[$i] / 3);
					if (($this->runLength[$i - 2] == $fact)
						&& ($this->runLength[$i - 1] == $fact)
						&& ($this->runLength[$i + 1] == $fact)
						&& ($this->runLength[$i + 2] == $fact)) {
						if (($this->runLength[$i - 3] < 0) || ($this->runLength[$i - 3] >= (4 * $fact))) {
							$demerit += N3;
						} elseif ((($i + 3) >= $length) || ($this->runLength[$i + 3] >= (4 * $fact))) {
							$demerit += N3;
						}
					}
				}
			}
		}
		return $demerit;
	}

	 /**
	  * evaluateSymbol
	   * @param  int   $width
	   * @param  array $frame
	   * @return int   demerit
	  */
	 protected function evaluateSymbol($width, $frame) {
		$head    = 0;
		$demerit = 0;
		for ($y = 0; $y < $width; ++$y) {
			$head               = 0;
			$this->runLength[0] = 1;
			$frameY             = $frame[$y];
			if ($y > 0) {
				$frameYM = $frame[$y - 1];
			}
			for ($x = 0; $x < $width; ++$x) {
				if (($x > 0) && ($y > 0)) {
					$b22 = \ord($frameY[$x]) & \ord($frameY[$x - 1]) & \ord($frameYM[$x]) & \ord($frameYM[$x - 1]);
					$w22 = \ord($frameY[$x]) | \ord($frameY[$x - 1]) | \ord($frameYM[$x]) | \ord($frameYM[$x - 1]);
					if (($b22 | ($w22 ^ 1)) & 1) {
						$demerit += N2;
					}
				}
				if (($x == 0) && (\ord($frameY[$x]) & 1)) {
					$this->runLength[0]     = -1;
					$head                   = 1;
					$this->runLength[$head] = 1;
				} elseif ($x > 0) {
					if ((\ord($frameY[$x]) ^ \ord($frameY[$x - 1])) & 1) {
						++$head;
						$this->runLength[$head] = 1;
					} else {
						++$this->runLength[$head];
					}
				}
			}
			$demerit += $this->calcN1N3($head + 1);
		}
		for ($x = 0; $x < $width; ++$x) {
			$head               = 0;
			$this->runLength[0] = 1;
			for ($y = 0; $y < $width; ++$y) {
				if (($y == 0) && (\ord($frame[$y][$x]) & 1)) {
					$this->runLength[0]     = -1;
					$head                   = 1;
					$this->runLength[$head] = 1;
				} elseif ($y > 0) {
					if ((\ord($frame[$y][$x]) ^ \ord($frame[$y - 1][$x])) & 1) {
						++$head;
						$this->runLength[$head] = 1;
					} else {
						++$this->runLength[$head];
					}
				}
			}
			$demerit += $this->calcN1N3($head + 1);
		}
		return $demerit;
	}

	 /**
	  * mask
	   * @param  int   $width
	   * @param  array $frame
	   * @param  int   $level
	   * @return array best mask
	  */
	 protected function mask($width, $frame, $level) {
		$minDemerit    = \PHP_INT_MAX;
		$bestMaskNum   = 0;
		$bestMask      = [];
		$checked_masks = [0, 1, 2, 3, 4, 5, 6, 7];
		if (QR_FIND_FROM_RANDOM !== false) {
			$howManuOut = 8 - (QR_FIND_FROM_RANDOM % 9);
			for ($i = 0; $i < $howManuOut; ++$i) {
				$remPos = \mt_rand(0, \count($checked_masks) - 1);
				unset($checked_masks[$remPos]);
				$checked_masks = \array_values($checked_masks);
			}
		}
		$bestMask = $frame;
		foreach ($checked_masks as $i) {
			$mask    = \array_fill(0, $width, \str_repeat("\0", $width));
			$demerit = 0;
			$blacks  = 0;
			$blacks  = $this->makeMaskNo($i, $width, $frame, $mask);
			$blacks += $this->writeFormatInformation($width, $mask, $i, $level);
			$blacks  = (int) (100 * $blacks / ($width * $width));
			$demerit = (int) ((int) (\abs($blacks - 50) / 5) * N4);
			$demerit += $this->evaluateSymbol($width, $mask);
			if ($demerit < $minDemerit) {
				$minDemerit  = $demerit;
				$bestMask    = $mask;
				$bestMaskNum = $i;
			}
		}
		return $bestMask;
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRsplit

	 /**
	  * Return true if the character at specified position is a number
	   * @param  string $str string
	   * @param  int    $pos characted position
	   * @return bool   true of false
	  */
	 protected function isdigitat($str, $pos) {
		if ($pos >= \strlen($str)) {
			return false;
		}
		return ((\ord($str[$pos]) >= \ord('0')) && (\ord($str[$pos]) <= \ord('9')));
	}

	 /**
	  * Return true if the character at specified position is an alphanumeric character
	   * @param  string $str string
	   * @param  int    $pos characted position
	   * @return bool   true of false
	  */
	 protected function isalnumat($str, $pos) {
		if ($pos >= \strlen($str)) {
			return false;
		}
		return ($this->lookAnTable(\ord($str[$pos])) >= 0);
	}

	 /**
	  * identifyMode
	   * @param  int $pos
	   * @return int mode
	  */
	 protected function identifyMode($pos) {
		if ($pos >= \strlen($this->dataStr)) {
			return QR_MODE_NL;
		}
		$c = $this->dataStr[$pos];
		if ($this->isdigitat($this->dataStr, $pos)) {
			return QR_MODE_NM;
		} elseif ($this->isalnumat($this->dataStr, $pos)) {
			return QR_MODE_AN;
		} elseif ($this->hint == QR_MODE_KJ) {
			if ($pos + 1 < \strlen($this->dataStr)) {
				$d    = $this->dataStr[$pos + 1];
				$word = (\ord($c) << 8) | \ord($d);
				if (($word >= 0x8140 && $word <= 0x9ffc) || ($word >= 0xe040 && $word <= 0xebbf)) {
					return QR_MODE_KJ;
				}
			}
		}
		return QR_MODE_8B;
	}

	 /**
	  * eatNum
	   * @return int run
	  */
	 protected function eatNum() {
		$ln = $this->lengthIndicator(QR_MODE_NM, $this->version);
		$p  = 0;
		while($this->isdigitat($this->dataStr, $p)) {
			++$p;
		}
		$run  = $p;
		$mode = $this->identifyMode($p);
		if ($mode == QR_MODE_8B) {
			$dif = $this->estimateBitsModeNum($run) + 4 + $ln
			+ $this->estimateBitsMode8(1)         // + 4 + l8
			- $this->estimateBitsMode8($run + 1); // - 4 - l8
			if ($dif > 0) {
				return $this->eat8();
			}
		}
		if ($mode == QR_MODE_AN) {
			$dif = $this->estimateBitsModeNum($run) + 4 + $ln
			+ $this->estimateBitsModeAn(1)        // + 4 + la
			- $this->estimateBitsModeAn($run + 1);// - 4 - la
			if ($dif > 0) {
				return $this->eatAn();
			}
		}
		$this->items = $this->appendNewInputItem($this->items, QR_MODE_NM, $run, \str_split($this->dataStr));
		return $run;
	}

	 /**
	  * eatAn
	   * @return int run
	  */
	 protected function eatAn() {
		$la = $this->lengthIndicator(QR_MODE_AN,  $this->version);
		$ln = $this->lengthIndicator(QR_MODE_NM, $this->version);
		$p  = 1;
		while($this->isalnumat($this->dataStr, $p)) {
			if ($this->isdigitat($this->dataStr, $p)) {
				$q = $p;
				while($this->isdigitat($this->dataStr, $q)) {
					++$q;
				}
				$dif = $this->estimateBitsModeAn($p) // + 4 + la
				+ $this->estimateBitsModeNum($q - $p) + 4 + $ln
				- $this->estimateBitsModeAn($q); // - 4 - la
				if ($dif < 0) {
					break;
				} else {
					$p = $q;
				}
			} else {
				++$p;
			}
		}
		$run = $p;
		if (!$this->isalnumat($this->dataStr, $p)) {
			$dif = $this->estimateBitsModeAn($run) + 4 + $la
			+ $this->estimateBitsMode8(1) // + 4 + l8
			- $this->estimateBitsMode8($run + 1); // - 4 - l8
			if ($dif > 0) {
				return $this->eat8();
			}
		}
		$this->items = $this->appendNewInputItem($this->items, QR_MODE_AN, $run, \str_split($this->dataStr));
		return $run;
	}

	 /**
	  * eatKanji
	   * @return int run
	  */
	 protected function eatKanji() {
		$p = 0;
		while($this->identifyMode($p) == QR_MODE_KJ) {
			$p += 2;
		}
		$this->items = $this->appendNewInputItem($this->items, QR_MODE_KJ, $p, \str_split($this->dataStr));
		$run         = $p;
		return $run;
	}

	 /**
	  * eat8
	   * @return int run
	  */
	 protected function eat8() {
		$la         = $this->lengthIndicator(QR_MODE_AN, $this->version);
		$ln         = $this->lengthIndicator(QR_MODE_NM, $this->version);
		$p          = 1;
		$dataStrLen = \strlen($this->dataStr);
		while($p < $dataStrLen) {
			$mode = $this->identifyMode($p);
			if ($mode == QR_MODE_KJ) {
				break;
			}
			if ($mode == QR_MODE_NM) {
				$q = $p;
				while($this->isdigitat($this->dataStr, $q)) {
					++$q;
				}
				$dif = $this->estimateBitsMode8($p) // + 4 + l8
				+ $this->estimateBitsModeNum($q - $p) + 4 + $ln
				- $this->estimateBitsMode8($q); // - 4 - l8
				if ($dif < 0) {
					break;
				} else {
					$p = $q;
				}
			} elseif ($mode == QR_MODE_AN) {
				$q = $p;
				while($this->isalnumat($this->dataStr, $q)) {
					++$q;
				}
				$dif = $this->estimateBitsMode8($p)  // + 4 + l8
				+ $this->estimateBitsModeAn($q - $p) + 4 + $la
				- $this->estimateBitsMode8($q); // - 4 - l8
				if ($dif < 0) {
					break;
				} else {
					$p = $q;
				}
			} else {
				++$p;
			}
		}
		$run         = $p;
		$this->items = $this->appendNewInputItem($this->items, QR_MODE_8B, $run, \str_split($this->dataStr));
		return $run;
	}

	 /**
	  * splitString
	   * @return int
	  */
	 protected function splitString() {
		while (\strlen($this->dataStr) > 0) {
			$mode = $this->identifyMode(0);
			switch ($mode) {
				case QR_MODE_NM: {
					$length = $this->eatNum();
					break;
				}
				case QR_MODE_AN: {
					$length = $this->eatAn();
					break;
				}
				case QR_MODE_KJ: {
					if ($this->hint == QR_MODE_KJ) {
						$length = $this->eatKanji();
					} else {
						$length = $this->eat8();
					}
					break;
				}
				default: {
					$length = $this->eat8();
					break;
				}
			}
			if ($length == 0) {
				return 0;
			}
			if ($length < 0) {
				return -1;
			}
			$this->dataStr = \substr($this->dataStr, $length);
		}
		return 0;
	}

	 /**
	  * toUpper
	  */
	 protected function toUpper() {
		$stringLen = \strlen($this->dataStr);
		$p         = 0;
		while ($p < $stringLen) {
			$mode = $this->identifyMode(\substr($this->dataStr, $p), $this->hint);
			if ($mode == QR_MODE_KJ) {
				$p += 2;
			} else {
				if ((\ord($this->dataStr[$p]) >= \ord('a')) && (\ord($this->dataStr[$p]) <= \ord('z'))) {
					$this->dataStr[$p] = \chr(\ord($this->dataStr[$p]) - 32);
				}
				++$p;
			}
		}
		return $this->dataStr;
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRinputItem

	 /**
	  * newInputItem
	   * @param  int   $mode
	   * @param  int   $size
	   * @param  array $data
	   * @param  array $bstream
	   * @return array input item
	  */
	 protected function newInputItem($mode, $size, $data, $bstream = null) {
		$setData = \array_slice($data, 0, $size);
		if (\count($setData) < $size) {
			$setData = \array_merge($setData, \array_fill(0, ($size - \count($setData)), 0));
		}
		if (!$this->check($mode, $size, $setData)) {
			return null;
		}
		$inputitem            = [];
		$inputitem['mode']    = $mode;
		$inputitem['size']    = $size;
		$inputitem['data']    = $setData;
		$inputitem['bstream'] = $bstream;
		return $inputitem;
	}

	 /**
	  * encodeModeNum
	   * @param  array $inputitem
	   * @param  int   $version
	   * @return array input item
	  */
	 protected function encodeModeNum($inputitem, $version) {
		$words                = (int) ($inputitem['size'] / 3);
		$inputitem['bstream'] = [];
		$val                  = 0x1;
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, $val);
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], $this->lengthIndicator(QR_MODE_NM, $version), $inputitem['size']);
		for ($i = 0; $i < $words; ++$i) {
			$val = (\ord($inputitem['data'][$i * 3  ]) - \ord('0')) * 100;
			$val += (\ord($inputitem['data'][$i * 3 + 1]) - \ord('0')) * 10;
			$val += (\ord($inputitem['data'][$i * 3 + 2]) - \ord('0'));
			$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 10, $val);
		}
		if ($inputitem['size'] - $words * 3 == 1) {
			$val                  = \ord($inputitem['data'][$words * 3]) - \ord('0');
			$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, $val);
		} elseif (($inputitem['size'] - ($words * 3)) == 2) {
			$val = (\ord($inputitem['data'][$words * 3  ]) - \ord('0')) * 10;
			$val += (\ord($inputitem['data'][$words * 3 + 1]) - \ord('0'));
			$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 7, $val);
		}
		return $inputitem;
	}

	 /**
	  * encodeModeAn
	   * @param  array $inputitem
	   * @param  int   $version
	   * @return array input item
	  */
	 protected function encodeModeAn($inputitem, $version) {
		$words                = (int) ($inputitem['size'] / 2);
		$inputitem['bstream'] = [];
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 0x02);
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], $this->lengthIndicator(QR_MODE_AN, $version), $inputitem['size']);
		for ($i = 0; $i < $words; ++$i) {
			$val = (int) ($this->lookAnTable(\ord($inputitem['data'][$i * 2])) * 45);
			$val += (int) ($this->lookAnTable(\ord($inputitem['data'][($i * 2) + 1])));
			$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 11, $val);
		}
		if ($inputitem['size'] & 1) {
			$val                  = $this->lookAnTable(\ord($inputitem['data'][($words * 2)]));
			$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 6, $val);
		}
		return $inputitem;
	}

	 /**
	  * encodeMode8
	   * @param  array $inputitem
	   * @param  int   $version
	   * @return array input item
	  */
	 protected function encodeMode8($inputitem, $version) {
		$inputitem['bstream'] = [];
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 0x4);
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], $this->lengthIndicator(QR_MODE_8B, $version), $inputitem['size']);
		for ($i = 0; $i < $inputitem['size']; ++$i) {
			$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 8, \ord($inputitem['data'][$i]));
		}
		return $inputitem;
	}

	 /**
	  * encodeModeKanji
	   * @param  array $inputitem
	   * @param  int   $version
	   * @return array input item
	  */
	 protected function encodeModeKanji($inputitem, $version) {
		$inputitem['bstream'] = [];
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 0x8);
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], $this->lengthIndicator(QR_MODE_KJ, $version), (int) ($inputitem['size'] / 2));
		for ($i = 0; $i < $inputitem['size']; $i += 2) {
			$val = (\ord($inputitem['data'][$i]) << 8) | \ord($inputitem['data'][$i + 1]);
			if ($val <= 0x9ffc) {
				$val -= 0x8140;
			} else {
				$val -= 0xc140;
			}
			$h                    = ($val >> 8) * 0xc0;
			$val                  = ($val & 0xff) + $h;
			$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 13, $val);
		}
		return $inputitem;
	}

	 /**
	  * encodeModeStructure
	   * @param  array $inputitem
	   * @return array input item
	  */
	 protected function encodeModeStructure($inputitem) {
		$inputitem['bstream'] = [];
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 0x03);
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, \ord($inputitem['data'][1]) - 1);
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, \ord($inputitem['data'][0]) - 1);
		$inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 8, \ord($inputitem['data'][2]));
		return $inputitem;
	}

	 /**
	  * encodeBitStream
	   * @param  array $inputitem
	   * @param  int   $version
	   * @return array input item
	  */
	 protected function encodeBitStream($inputitem, $version) {
		$inputitem['bstream'] = [];
		$words                = $this->maximumWords($inputitem['mode'], $version);
		if ($inputitem['size'] > $words) {
			$st1                  = $this->newInputItem($inputitem['mode'], $words, $inputitem['data']);
			$st2                  = $this->newInputItem($inputitem['mode'], $inputitem['size'] - $words, \array_slice($inputitem['data'], $words));
			$st1                  = $this->encodeBitStream($st1, $version);
			$st2                  = $this->encodeBitStream($st2, $version);
			$inputitem['bstream'] = [];
			$inputitem['bstream'] = $this->appendBitstream($inputitem['bstream'], $st1['bstream']);
			$inputitem['bstream'] = $this->appendBitstream($inputitem['bstream'], $st2['bstream']);
		} else {
			switch($inputitem['mode']) {
				case QR_MODE_NM: {
					$inputitem = $this->encodeModeNum($inputitem, $version);
					break;
				}
				case QR_MODE_AN: {
					$inputitem = $this->encodeModeAn($inputitem, $version);
					break;
				}
				case QR_MODE_8B: {
					$inputitem = $this->encodeMode8($inputitem, $version);
					break;
				}
				case QR_MODE_KJ: {
					$inputitem = $this->encodeModeKanji($inputitem, $version);
					break;
				}
				case QR_MODE_ST: {
					$inputitem = $this->encodeModeStructure($inputitem);
					break;
				}
				default: {
					break;
				}
			}
		}
		return $inputitem;
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRinput

	/**
	 * Append data to an input object.
	 * The data is copied and appended to the input object.
	 * @param  array $items input items
	 * @param  int   $mode  encoding mode
	 * @param  int   $size  size of data (byte)
	 * @param  array $data  array of input data
	 * @return array items
	 */
	protected function appendNewInputItem($items, $mode, $size, $data) {
		$newitem = $this->newInputItem($mode, $size, $data);
		if (!empty($newitem)) {
			$items[] = $newitem;
		}
		return $items;
	}

	 /**
	  * insertStructuredAppendHeader
	   * @param  array $items
	   * @param  int   $size
	   * @param  int   $index
	   * @param  int   $parity
	   * @return array items
	  */
	 protected function insertStructuredAppendHeader($items, $size, $index, $parity) {
		if ($size > MAX_STRUCTURED_SYMBOLS) {
			return -1;
		}
		if (($index <= 0) || ($index > MAX_STRUCTURED_SYMBOLS)) {
			return -1;
		}
		$buf   = [$size, $index, $parity];
		$entry = $this->newInputItem(QR_MODE_ST, 3, buf);
		\array_unshift($items, $entry);
		return $items;
	}

	 /**
	  * calcParity
	   * @param  array $items
	   * @return int   parity
	  */
	 protected function calcParity($items) {
		$parity = 0;
		foreach ($items as $item) {
			if ($item['mode'] != QR_MODE_ST) {
				for ($i = $item['size'] - 1; $i >= 0; --$i) {
					$parity ^= $item['data'][$i];
				}
			}
		}
		return $parity;
	}

	 /**
	  * checkModeNum
	   * @param  int   $size
	   * @param  array $data
	   * @return bool  true or false
	  */
	 protected function checkModeNum($size, $data) {
		for ($i = 0; $i < $size; ++$i) {
			if ((\ord($data[$i]) < \ord('0')) || (\ord($data[$i]) > \ord('9'))){
				return false;
			}
		}
		return true;
	}

	/**
	 * Look up the alphabet-numeric conversion table (see JIS X0510:2004, pp.19).
	 * @param  int $c character value
	 * @return int value
	 */
	protected function lookAnTable($c) {
		return (($c > 127)?-1:$this->anTable[$c]);
	}

	 /**
	  * checkModeAn
	   * @param  int   $size
	   * @param  array $data
	   * @return bool  true or false
	  */
	 protected function checkModeAn($size, $data) {
		for ($i = 0; $i < $size; ++$i) {
			if ($this->lookAnTable(\ord($data[$i])) == -1) {
				return false;
			}
		}
		return true;
	}

	 /**
	  * estimateBitsModeNum
	   * @param  int $size
	   * @return int number of bits
	  */
	 protected function estimateBitsModeNum($size) {
		$w    = (int) ($size / 3);
		$bits = ($w * 10);
		switch($size - ($w * 3)) {
			case 1: {
				$bits += 4;
				break;
			}
			case 2: {
				$bits += 7;
				break;
			}
		}
		return $bits;
	}

	 /**
	  * estimateBitsModeAn
	   * @param  int $size
	   * @return int number of bits
	  */
	 protected function estimateBitsModeAn($size) {
		$bits = (int) ($size * 5.5); // (size / 2 ) * 11
		if ($size & 1) {
			$bits += 6;
		}
		return $bits;
	}

	 /**
	  * estimateBitsMode8
	   * @param  int $size
	   * @return int number of bits
	  */
	 protected function estimateBitsMode8($size) {
		return (int) ($size * 8);
	}

	 /**
	  * estimateBitsModeKanji
	   * @param  int $size
	   * @return int number of bits
	  */
	 protected function estimateBitsModeKanji($size) {
		return (int) ($size * 6.5); // (size / 2 ) * 13
	}

	 /**
	  * checkModeKanji
	   * @param  int   $size
	   * @param  array $data
	   * @return bool  true or false
	  */
	 protected function checkModeKanji($size, $data) {
		if ($size & 1) {
			return false;
		}
		for ($i = 0; $i < $size; $i += 2) {
			$val = (\ord($data[$i]) << 8) | \ord($data[$i + 1]);
			if (($val < 0x8140) || (($val > 0x9ffc) && ($val < 0xe040)) || ($val > 0xebbf)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Validate the input data.
	 * @param  int   $mode encoding mode
	 * @param  int   $size size of data (byte)
	 * @param  array $data data to validate
	 * @return bool  true in case of valid data, false otherwise
	 */
	protected function check($mode, $size, $data) {
		if ($size <= 0) {
			return false;
		}
		switch($mode) {
			case QR_MODE_NM: {
				return $this->checkModeNum($size, $data);
			}
			case QR_MODE_AN: {
				return $this->checkModeAn($size, $data);
			}
			case QR_MODE_KJ: {
				return $this->checkModeKanji($size, $data);
			}
			case QR_MODE_8B: {
				return true;
			}
			case QR_MODE_ST: {
				return true;
			}
			default: {
				break;
			}
		}
		return false;
	}

	 /**
	  * estimateBitStreamSize
	   * @param  array $items
	   * @param  int   $version
	   * @return int   bits
	  */
	 protected function estimateBitStreamSize($items, $version) {
		$bits = 0;
		if ($version == 0) {
			$version = 1;
		}
		foreach ($items as $item) {
			switch($item['mode']) {
				case QR_MODE_NM: {
					$bits = $this->estimateBitsModeNum($item['size']);
					break;
				}
				case QR_MODE_AN: {
					$bits = $this->estimateBitsModeAn($item['size']);
					break;
				}
				case QR_MODE_8B: {
					$bits = $this->estimateBitsMode8($item['size']);
					break;
				}
				case QR_MODE_KJ: {
					$bits = $this->estimateBitsModeKanji($item['size']);
					break;
				}
				case QR_MODE_ST: {
					return STRUCTURE_HEADER_BITS;
				}
				default: {
					return 0;
				}
			}
			$l   = $this->lengthIndicator($item['mode'], $version);
			$m   = 1 << $l;
			$num = (int) (($item['size'] + $m - 1) / $m);
			$bits += $num * (4 + $l);
		}
		return $bits;
	}

	 /**
	  * estimateVersion
	   * @param  array $items
	   * @return int   version
	  */
	 protected function estimateVersion($items) {
		$version = 0;
		$prev    = 0;
		do {
			$prev    = $version;
			$bits    = $this->estimateBitStreamSize($items, $prev);
			$version = $this->getMinimumVersion((int) (($bits + 7) / 8), $this->level);
			if ($version < 0) {
				return -1;
			}
		} while ($version > $prev);
		return $version;
	}

	 /**
	  * lengthOfCode
	   * @param  int $mode
	   * @param  int $version
	   * @param  int $bits
	   * @return int size
	  */
	 protected function lengthOfCode($mode, $version, $bits) {
		$payload = $bits - 4 - $this->lengthIndicator($mode, $version);
		switch($mode) {
			case QR_MODE_NM: {
				$chunks = (int) ($payload / 10);
				$remain = $payload - $chunks * 10;
				$size   = $chunks * 3;
				if ($remain >= 7) {
					$size += 2;
				} elseif ($remain >= 4) {
					++$size;
				}
				break;
			}
			case QR_MODE_AN: {
				$chunks = (int) ($payload / 11);
				$remain = $payload - $chunks * 11;
				$size   = $chunks * 2;
				if ($remain >= 6) {
					++$size;
				}
				break;
			}
			case QR_MODE_8B: {
				$size = (int) ($payload / 8);
				break;
			}
			case QR_MODE_KJ: {
				$size = (int) (($payload / 13) * 2);
				break;
			}
			case QR_MODE_ST: {
				$size = (int) ($payload / 8);
				break;
			}
			default: {
				$size = 0;
				break;
			}
		}
		$maxsize = $this->maximumWords($mode, $version);
		if ($size < 0) {
			$size = 0;
		}
		if ($size > $maxsize) {
			$size = $maxsize;
		}
		return $size;
	}

	 /**
	  * createBitStream
	   * @param  array $items
	   * @return array of items and total bits
	  */
	 protected function createBitStream($items) {
		$total = 0;
		foreach ($items as $key => $item) {
			$items[$key] = $this->encodeBitStream($item, $this->version);
			$bits        = \count($items[$key]['bstream']);
			$total += $bits;
		}
		return [$items, $total];
	}

	 /**
	  * convertData
	   * @param  array $items
	   * @return array items
	  */
	 protected function convertData($items) {
		$ver = $this->estimateVersion($items);
		if ($ver > $this->version) {
			$this->version = $ver;
		}
		while (true) {
			$cbs   = $this->createBitStream($items);
			$items = $cbs[0];
			$bits  = $cbs[1];
			if ($bits < 0) {
				return -1;
			}
			$ver = $this->getMinimumVersion((int) (($bits + 7) / 8), $this->level);
			if ($ver < 0) {
				return -1;
			} elseif ($ver > $this->version) {
				$this->version = $ver;
			} else {
				break;
			}
		}
		return $items;
	}

	 /**
	  * Append Padding Bit to bitstream
	   * @param  array $bstream
	   * @return array bitstream
	  */
	 protected function appendPaddingBit($bstream) {
	 	if ($bstream === null) {
	 		return null;
	 	}
		$bits     = \count($bstream);
		$maxwords = $this->getDataLength($this->version, $this->level);
		$maxbits  = $maxwords * 8;
		if ($maxbits == $bits) {
			return $bstream;
		}
		if ($maxbits - $bits < 5) {
			return $this->appendNum($bstream, $maxbits - $bits, 0);
		}
		$bits += 4;
		$words   = (int) (($bits + 7) / 8);
		$padding = [];
		$padding = $this->appendNum($padding, $words * 8 - $bits + 4, 0);
		$padlen  = $maxwords - $words;
		if ($padlen > 0) {
			$padbuf = [];
			for ($i = 0; $i < $padlen; ++$i) {
				$padbuf[$i] = ($i & 1)?0x11:0xec;
			}
			$padding = $this->appendBytes($padding, $padlen, $padbuf);
		}
		return $this->appendBitstream($bstream, $padding);
	}

	 /**
	  * mergeBitStream
	   * @param  array $items items
	   * @return array bitstream
	  */
	 protected function mergeBitStream($items) {
		$items = $this->convertData($items);
		if (!\is_array($items)) {
			return null;
		}
		$bstream = [];
		foreach ($items as $item) {
			$bstream = $this->appendBitstream($bstream, $item['bstream']);
		}
		return $bstream;
	}

	/**
	 * Returns a stream of bits.
	 * @param  int   $items
	 * @return array padded merged byte stream
	 */
	protected function getBitStream($items) {
		$bstream = $this->mergeBitStream($items);
		return $this->appendPaddingBit($bstream);
	}

	/**
	 * Pack all bit streams padding bits into a byte array.
	 * @param  int   $items
	 * @return array padded merged byte stream
	 */
	protected function getByteStream($items) {
		$bstream = $this->getBitStream($items);
		return $this->bitstreamToByte($bstream);
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRbitstream

	 /**
	  * Return an array with zeros
	   * @param  int   $setLength array size
	   * @return array
	  */
	 protected function allocate($setLength) {
		return \array_fill(0, $setLength, 0);
	}

	 /**
	  * Return new bitstream from number
	   * @param  int   $bits number of bits
	   * @param  int   $num  number
	   * @return array bitstream
	  */
	 protected function newFromNum($bits, $num) {
		$bstream = $this->allocate($bits);
		$mask    = 1 << ($bits - 1);
		for ($i = 0; $i < $bits; ++$i) {
			if ($num & $mask) {
				$bstream[$i] = 1;
			} else {
				$bstream[$i] = 0;
			}
			$mask = $mask >> 1;
		}
		return $bstream;
	}

	 /**
	  * Return new bitstream from bytes
	   * @param  int   $size size
	   * @param  array $data bytes
	   * @return array bitstream
	  */
	 protected function newFromBytes($size, $data) {
		$bstream = $this->allocate($size * 8);
		$p       = 0;
		for ($i = 0; $i < $size; ++$i) {
			$mask = 0x80;
			for ($j = 0; $j < 8; ++$j) {
				if ($data[$i] & $mask) {
					$bstream[$p] = 1;
				} else {
					$bstream[$p] = 0;
				}
				++$p;
				$mask = $mask >> 1;
			}
		}
		return $bstream;
	}

	 /**
	  * Append one bitstream to another
	   * @param  array $bitstream original bitstream
	   * @param  array $append    bitstream to append
	   * @return array bitstream
	  */
	 protected function appendBitstream($bitstream, $append) {
		if ((!\is_array($append)) || (\count($append) == 0)) {
			return $bitstream;
		}
		if (\count($bitstream) == 0) {
			return $append;
		}
		return \array_values(\array_merge($bitstream, $append));
	}

	 /**
	  * Append one bitstream created from number to another
	   * @param  array $bitstream original bitstream
	   * @param  int   $bits      number of bits
	   * @param  int   $num       number
	   * @return array bitstream
	  */
	 protected function appendNum($bitstream, $bits, $num) {
		if ($bits == 0) {
			return 0;
		}
		$b = $this->newFromNum($bits, $num);
		return $this->appendBitstream($bitstream, $b);
	}

	 /**
	  * Append one bitstream created from bytes to another
	   * @param  array $bitstream original bitstream
	   * @param  int   $size      size
	   * @param  array $data      bytes
	   * @return array bitstream
	  */
	 protected function appendBytes($bitstream, $size, $data) {
		if ($size == 0) {
			return 0;
		}
		$b = $this->newFromBytes($size, $data);
		return $this->appendBitstream($bitstream, $b);
	}

	 /**
	  * Convert bitstream to bytes
	   * @param  array $bstream original bitstream
	   * @return array of bytes
	  */
	 protected function bitstreamToByte($bstream) {
		if ($bstream === null) {
	 		return null;
	 	}
		$size = \count($bstream);
		if ($size == 0) {
			return [];
		}
		$data  = \array_fill(0, (int) (($size + 7) / 8), 0);
		$bytes = (int) ($size / 8);
		$p     = 0;
		for ($i = 0; $i < $bytes; ++$i) {
			$v = 0;
			for ($j = 0; $j < 8; ++$j) {
				$v = $v << 1;
				$v |= $bstream[$p];
				++$p;
			}
			$data[$i] = $v;
		}
		if ($size & 7) {
			$v = 0;
			for ($j = 0; $j < ($size & 7); ++$j) {
				$v = $v << 1;
				$v |= $bstream[$p];
				++$p;
			}
			$data[$bytes] = $v;
		}
		return $data;
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRspec

	 /**
	  * Replace a value on the array at the specified position
	   * @param  array  $srctab
	   * @param  int    $x       X position
	   * @param  int    $y       Y position
	   * @param  string $repl    value to replace
	   * @param  int    $replLen length of the repl string
	   * @return array  srctab
	  */
	 protected function qrstrset($srctab, $x, $y, $repl, $replLen = false) {
		$srctab[$y] = \substr_replace($srctab[$y], ($replLen !== false)?\substr($repl,0,$replLen):$repl, $x, ($replLen !== false)?$replLen:\strlen($repl));
		return $srctab;
	}

	/**
	 * Return maximum data code length (bytes) for the version.
	 * @param  int $version version
	 * @param  int $level   error correction level
	 * @return int maximum size (bytes)
	 */
	protected function getDataLength($version, $level) {
		return $this->capacity[$version][QRCAP_WORDS] - $this->capacity[$version][QRCAP_EC][$level];
	}

	/**
	 * Return maximum error correction code length (bytes) for the version.
	 * @param  int $version version
	 * @param  int $level   error correction level
	 * @return int ECC size (bytes)
	 */
	protected function getECCLength($version, $level){
		return $this->capacity[$version][QRCAP_EC][$level];
	}

	/**
	 * Return the width of the symbol for the version.
	 * @param  int $version version
	 * @return int width
	 */
	protected function getWidth($version) {
		return $this->capacity[$version][QRCAP_WIDTH];
	}

	/**
	 * Return the numer of remainder bits.
	 * @param  int $version version
	 * @return int number of remainder bits
	 */
	protected function getRemainder($version) {
		return $this->capacity[$version][QRCAP_REMINDER];
	}

	/**
	 * Return a version number that satisfies the input code length.
	 * @param  int $size  input code length (bytes)
	 * @param  int $level error correction level
	 * @return int version number
	 */
	protected function getMinimumVersion($size, $level) {
		for ($i = 1; $i <= QRSPEC_VERSION_MAX; ++$i) {
			$words = ($this->capacity[$i][QRCAP_WORDS] - $this->capacity[$i][QRCAP_EC][$level]);
			if ($words >= $size) {
				return $i;
			}
		}
		// the size of input data is greater than QR capacity, try to lover the error correction mode
		return -1;
	}

	/**
	 * Return the size of length indicator for the mode and version.
	 * @param  int $mode    encoding mode
	 * @param  int $version version
	 * @return int the size of the appropriate length indicator (bits)
	 */
	protected function lengthIndicator($mode, $version) {
		if ($mode == QR_MODE_ST) {
			return 0;
		}
		if ($version <= 9) {
			$l = 0;
		} elseif ($version <= 26) {
			$l = 1;
		} else {
			$l = 2;
		}
		return $this->lengthTableBits[$mode][$l];
	}

	/**
	 * Return the maximum length for the mode and version.
	 * @param  int $mode    encoding mode
	 * @param  int $version version
	 * @return int the maximum length (bytes)
	 */
	protected function maximumWords($mode, $version) {
		if ($mode == QR_MODE_ST) {
			return 3;
		}
		if ($version <= 9) {
			$l = 0;
		} elseif ($version <= 26) {
			$l = 1;
		} else {
			$l = 2;
		}
		$bits  = $this->lengthTableBits[$mode][$l];
		$words = (1 << $bits) - 1;
		if ($mode == QR_MODE_KJ) {
			$words *= 2; // the number of bytes is required
		}
		return $words;
	}

	/**
	 * Return an array of ECC specification.
	 * @param  int   $version version
	 * @param  int   $level   error correction level
	 * @param  array $spec    an array of ECC specification contains as following: {# of type1 blocks, # of data code, # of ecc code, # of type2 blocks, # of data code}
	 * @return array spec
	 */
	protected function getEccSpec($version, $level, $spec) {
		if (\count($spec) < 5) {
			$spec = [0, 0, 0, 0, 0];
		}
		$b1   = $this->eccTable[$version][$level][0];
		$b2   = $this->eccTable[$version][$level][1];
		$data = $this->getDataLength($version, $level);
		$ecc  = $this->getECCLength($version, $level);
		if ($b2 == 0) {
			$spec[0] = $b1;
			$spec[1] = (int) ($data / $b1);
			$spec[2] = (int) ($ecc / $b1);
			$spec[3] = 0;
			$spec[4] = 0;
		} else {
			$spec[0] = $b1;
			$spec[1] = (int) ($data / ($b1 + $b2));
			$spec[2] = (int) ($ecc / ($b1 + $b2));
			$spec[3] = $b2;
			$spec[4] = $spec[1] + 1;
		}
		return $spec;
	}

	/**
	 * Put an alignment marker.
	 * @param  array $frame frame
	 * @param  int   $ox    X center coordinate of the pattern
	 * @param  int   $oy    Y center coordinate of the pattern
	 * @return array frame
	 */
	protected function putAlignmentMarker($frame, $ox, $oy) {
		$finder = [
			"\xa1\xa1\xa1\xa1\xa1",
			"\xa1\xa0\xa0\xa0\xa1",
			"\xa1\xa0\xa1\xa0\xa1",
			"\xa1\xa0\xa0\xa0\xa1",
			"\xa1\xa1\xa1\xa1\xa1",
			];
		$yStart = $oy - 2;
		$xStart = $ox - 2;
		for ($y = 0; $y < 5; ++$y) {
			$frame = $this->qrstrset($frame, $xStart, $yStart + $y, $finder[$y]);
		}
		return $frame;
	}

	 /**
	  * Put an alignment pattern.
	   * @param  int   $version version
	   * @param  array $frame   frame
	   * @param  int   $width   width
	   * @return array frame
	  */
	 protected function putAlignmentPattern($version, $frame, $width) {
		if ($version < 2) {
			return $frame;
		}
		$d = $this->alignmentPattern[$version][1] - $this->alignmentPattern[$version][0];
		if ($d < 0) {
			$w = 2;
		} else {
			$w = (int) (($width - $this->alignmentPattern[$version][0]) / $d + 2);
		}
		if ($w * $w - 3 == 1) {
			$x     = $this->alignmentPattern[$version][0];
			$y     = $this->alignmentPattern[$version][0];
			$frame = $this->putAlignmentMarker($frame, $x, $y);
			return $frame;
		}
		$cx = $this->alignmentPattern[$version][0];
		$wo = $w - 1;
		for ($x = 1; $x < $wo; ++$x) {
			$frame = $this->putAlignmentMarker($frame, 6, $cx);
			$frame = $this->putAlignmentMarker($frame, $cx,  6);
			$cx += $d;
		}
		$cy = $this->alignmentPattern[$version][0];
		for ($y = 0; $y < $wo; ++$y) {
			$cx = $this->alignmentPattern[$version][0];
			for ($x = 0; $x < $wo; ++$x) {
				$frame = $this->putAlignmentMarker($frame, $cx, $cy);
				$cx += $d;
			}
			$cy += $d;
		}
		return $frame;
	}

	/**
	 * Return BCH encoded version information pattern that is used for the symbol of version 7 or greater. Use lower 18 bits.
	 * @param  int    $version version
	 * @return string BCH encoded version information pattern
	 */
	protected function getVersionPattern($version) {
		if (($version < 7) || ($version > QRSPEC_VERSION_MAX)) {
			return 0;
		}
		return $this->versionPattern[($version - 7)];
	}

	/**
	 * Return BCH encoded format information pattern.
	 * @param  array  $mask
	 * @param  int    $level error correction level
	 * @return string BCH encoded format information pattern
	 */
	protected function getFormatInfo($mask, $level) {
		if (($mask < 0) || ($mask > 7)) {
			return 0;
		}
		if (($level < 0) || ($level > 3)) {
			return 0;
		}
		return $this->formatInfo[$level][$mask];
	}

	/**
	 * Put a finder pattern.
	 * @param  array $frame frame
	 * @param  int   $ox    X center coordinate of the pattern
	 * @param  int   $oy    Y center coordinate of the pattern
	 * @return array frame
	 */
	protected function putFinderPattern($frame, $ox, $oy) {
		$finder = [
		"\xc1\xc1\xc1\xc1\xc1\xc1\xc1",
		"\xc1\xc0\xc0\xc0\xc0\xc0\xc1",
		"\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
		"\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
		"\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
		"\xc1\xc0\xc0\xc0\xc0\xc0\xc1",
		"\xc1\xc1\xc1\xc1\xc1\xc1\xc1",
		];
		for ($y = 0; $y < 7; ++$y) {
			$frame = $this->qrstrset($frame, $ox, ($oy + $y), $finder[$y]);
		}
		return $frame;
	}

	/**
	 * Return a copy of initialized frame.
	 * @param  int   $version version
	 * @return array array of unsigned char
	 */
	protected function createFrame($version) {
		$width     = $this->capacity[$version][QRCAP_WIDTH];
		$frameLine = \str_repeat("\0", $width);
		$frame     = \array_fill(0, $width, $frameLine);
		// Finder pattern
		$frame = $this->putFinderPattern($frame, 0, 0);
		$frame = $this->putFinderPattern($frame, $width - 7, 0);
		$frame = $this->putFinderPattern($frame, 0, $width - 7);
		// Separator
		$yOffset = $width - 7;
		for ($y = 0; $y < 7; ++$y) {
			$frame[$y][7]          = "\xc0";
			$frame[$y][$width - 8] = "\xc0";
			$frame[$yOffset][7]    = "\xc0";
			++$yOffset;
		}
		$setPattern = \str_repeat("\xc0", 8);
		$frame      = $this->qrstrset($frame, 0, 7, $setPattern);
		$frame      = $this->qrstrset($frame, $width - 8, 7, $setPattern);
		$frame      = $this->qrstrset($frame, 0, $width - 8, $setPattern);
		// Format info
		$setPattern = \str_repeat("\x84", 9);
		$frame      = $this->qrstrset($frame, 0, 8, $setPattern);
		$frame      = $this->qrstrset($frame, $width - 8, 8, $setPattern, 8);
		$yOffset    = $width - 8;
		for ($y = 0; $y < 8; ++$y,++$yOffset) {
			$frame[$y][8]       = "\x84";
			$frame[$yOffset][8] = "\x84";
		}
		// Timing pattern
		$wo = $width - 15;
		for ($i = 1; $i < $wo; ++$i) {
			$frame[6][7 + $i] = \chr(0x90 | ($i & 1));
			$frame[7 + $i][6] = \chr(0x90 | ($i & 1));
		}
		// Alignment pattern
		$frame = $this->putAlignmentPattern($version, $frame, $width);
		// Version information
		if ($version >= 7) {
			$vinf = $this->getVersionPattern($version);
			$v    = $vinf;
			for ($x = 0; $x < 6; ++$x) {
				for ($y = 0; $y < 3; ++$y) {
					$frame[($width - 11) + $y][$x] = \chr(0x88 | ($v & 1));
					$v                             = $v >> 1;
				}
			}
			$v = $vinf;
			for ($y = 0; $y < 6; ++$y) {
				for ($x = 0; $x < 3; ++$x) {
					$frame[$y][$x + ($width - 11)] = \chr(0x88 | ($v & 1));
					$v                             = $v >> 1;
				}
			}
		}
		// and a little bit...
		$frame[$width - 8][8] = "\x81";
		return $frame;
	}

	/**
	 * Set new frame for the specified version.
	 * @param  int   $version version
	 * @return array array of unsigned char
	 */
	protected function newFrame($version) {
		if (($version < 1) || ($version > QRSPEC_VERSION_MAX)) {
			return null;
		}
		if (!isset($this->frames[$version])) {
			$this->frames[$version] = $this->createFrame($version);
		}
		if ($this->frames[$version] === null) {
			return null;
		}
		return $this->frames[$version];
	}

	 /**
	  * Return block number 0
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsBlockNum($spec) {
		return ($spec[0] + $spec[3]);
	}

	 /**
	  * Return block number 1
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsBlockNum1($spec) {
		return $spec[0];
	}

	 /**
	  * Return data codes 1
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsDataCodes1($spec) {
		return $spec[1];
	}

	 /**
	  * Return ecc codes 1
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsEccCodes1($spec) {
		return $spec[2];
	}

	 /**
	  * Return block number 2
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsBlockNum2($spec) {
		return $spec[3];
	}

	 /**
	  * Return data codes 2
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsDataCodes2($spec) {
		return $spec[4];
	}

	 /**
	  * Return ecc codes 2
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsEccCodes2($spec) {
		return $spec[2];
	}

	 /**
	  * Return data length
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsDataLength($spec) {
		return ($spec[0] * $spec[1]) + ($spec[3] * $spec[4]);
	}

	 /**
	  * Return ecc length
	   * @param  array $spec
	   * @return int   value
	  */
	 protected function rsEccLength($spec) {
		return ($spec[0] + $spec[3]) * $spec[2];
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRrs

	 /**
	  * Initialize a Reed-Solomon codec and add it to existing rsitems
	   * @param  int   $symsize symbol size, bits
	   * @param  int   $gfpoly  Field generator polynomial coefficients
	   * @param  int   $fcr     first root of RS code generator polynomial, index form
	   * @param  int   $prim    primitive element to generate polynomial roots
	   * @param  int   $nroots  RS code generator polynomial degree (number of roots)
	   * @param  int   $pad     padding bytes at front of shortened block
	   * @return array array of RS values:<ul><li>mm = Bits per symbol;</li><li>nn = Symbols per block;</li><li>alpha_to = log lookup table array;</li><li>index_of = Antilog lookup table array;</li><li>genpoly = Generator polynomial array;</li><li>nroots = Number of generator;</li><li>roots = number of parity symbols;</li><li>fcr = First consecutive root, index form;</li><li>prim = Primitive element, index form;</li><li>iprim = prim-th root of 1, index form;</li><li>pad = Padding bytes in shortened block;</li><li>gfpoly</ul>
	  */
	 protected function init_rs($symsize, $gfpoly, $fcr, $prim, $nroots, $pad) {
		foreach ($this->rsitems as $rs) {
			if (($rs['pad'] != $pad) || ($rs['nroots'] != $nroots) || ($rs['mm'] != $symsize)
				|| ($rs['gfpoly'] != $gfpoly) || ($rs['fcr'] != $fcr) || ($rs['prim'] != $prim)) {
				continue;
			}
			return $rs;
		}
		$rs = $this->init_rs_char($symsize, $gfpoly, $fcr, $prim, $nroots, $pad);
		\array_unshift($this->rsitems, $rs);
		return $rs;
	}

	// - - - - - - - - - - - - - - - - - - - - - - - - -

	// QRrsItem

	 /**
	  * modnn
	   * @param  array $rs RS values
	   * @param  int   $x  X position
	   * @return int   X osition
	  */
	 protected function modnn($rs, $x) {
		while ($x >= $rs['nn']) {
			$x -= $rs['nn'];
			$x = ($x >> $rs['mm']) + ($x & $rs['nn']);
		}
		return $x;
	}

	/**
	 * Initialize a Reed-Solomon codec and returns an array of values.
	 * @param  int   $symsize symbol size, bits
	 * @param  int   $gfpoly  Field generator polynomial coefficients
	 * @param  int   $fcr     first root of RS code generator polynomial, index form
	 * @param  int   $prim    primitive element to generate polynomial roots
	 * @param  int   $nroots  RS code generator polynomial degree (number of roots)
	 * @param  int   $pad     padding bytes at front of shortened block
	 * @return array array of RS values:<ul><li>mm = Bits per symbol;</li><li>nn = Symbols per block;</li><li>alpha_to = log lookup table array;</li><li>index_of = Antilog lookup table array;</li><li>genpoly = Generator polynomial array;</li><li>nroots = Number of generator;</li><li>roots = number of parity symbols;</li><li>fcr = First consecutive root, index form;</li><li>prim = Primitive element, index form;</li><li>iprim = prim-th root of 1, index form;</li><li>pad = Padding bytes in shortened block;</li><li>gfpoly</ul>
	 */
	protected function init_rs_char($symsize, $gfpoly, $fcr, $prim, $nroots, $pad) {
		// Based on Reed solomon encoder by Phil Karn, KA9Q (GNU-LGPLv2)
		$rs = null;
		// Check parameter ranges
		if (($symsize < 0) || ($symsize > 8)) {
			return $rs;
		}
		if (($fcr < 0) || ($fcr >= (1 << $symsize))) {
			return $rs;
		}
		if (($prim <= 0) || ($prim >= (1 << $symsize))) {
			return $rs;
		}
		if (($nroots < 0) || ($nroots >= (1 << $symsize))) {
			return $rs;
		}
		if (($pad < 0) || ($pad >= ((1 << $symsize) - 1 - $nroots))) {
			return $rs;
		}
		$rs             = [];
		$rs['mm']       = $symsize;
		$rs['nn']       = (1 << $symsize) - 1;
		$rs['pad']      = $pad;
		$rs['alpha_to'] = \array_fill(0, ($rs['nn'] + 1), 0);
		$rs['index_of'] = \array_fill(0, ($rs['nn'] + 1), 0);
		// PHP style macro replacement ;)
		$NN = & $rs['nn'];
		$A0 = & $NN;
		// Generate Galois field lookup tables
		$rs['index_of'][0]   = $A0; // log(zero) = -inf
		$rs['alpha_to'][$A0] = 0; // alpha**-inf = 0
		$sr                  = 1;
		for ($i = 0; $i < $rs['nn']; ++$i) {
			$rs['index_of'][$sr] = $i;
			$rs['alpha_to'][$i]  = $sr;
			$sr <<= 1;
			if ($sr & (1 << $symsize)) {
				$sr ^= $gfpoly;
			}
			$sr &= $rs['nn'];
		}
		if ($sr != 1) {
			// field generator polynomial is not primitive!
			return null;
		}
		// Form RS code generator polynomial from its roots
		$rs['genpoly'] = \array_fill(0, ($nroots + 1), 0);
		$rs['fcr']     = $fcr;
		$rs['prim']    = $prim;
		$rs['nroots']  = $nroots;
		$rs['gfpoly']  = $gfpoly;
		// Find prim-th root of 1, used in decoding
		for ($iprim = 1; ($iprim % $prim) != 0; $iprim += $rs['nn']) {
			 // intentional empty-body loop!
		}
		$rs['iprim']      = (int) ($iprim / $prim);
		$rs['genpoly'][0] = 1;
		for ($i = 0,$root = $fcr * $prim; $i < $nroots; $i++, $root += $prim) {
			$rs['genpoly'][$i + 1] = 1;
			// Multiply rs->genpoly[] by  @**(root + x)
			for ($j = $i; $j > 0; --$j) {
				if ($rs['genpoly'][$j] != 0) {
					$rs['genpoly'][$j] = $rs['genpoly'][$j - 1] ^ $rs['alpha_to'][$this->modnn($rs, $rs['index_of'][$rs['genpoly'][$j]] + $root)];
				} else {
					$rs['genpoly'][$j] = $rs['genpoly'][$j - 1];
				}
			}
			// rs->genpoly[0] can never be zero
			$rs['genpoly'][0] = $rs['alpha_to'][$this->modnn($rs, $rs['index_of'][$rs['genpoly'][0]] + $root)];
		}
		// convert rs->genpoly[] to index form for quicker encoding
		for ($i = 0; $i <= $nroots; ++$i) {
			$rs['genpoly'][$i] = $rs['index_of'][$rs['genpoly'][$i]];
		}
		return $rs;
	}

	 /**
	  * Encode a Reed-Solomon codec and returns the parity array
	   * @param  array  $rs     RS values
	   * @param  array  $data   data
	   * @param  array  $parity parity
	   * @return parity array
	  */
	 protected function encode_rs_char($rs, $data, $parity) {
		$MM       = & $rs['mm']; // bits per symbol
		$NN       = & $rs['nn']; // the total number of symbols in a RS block
		$ALPHA_TO = & $rs['alpha_to']; // the address of an array of NN elements to convert Galois field elements in index (log) form to polynomial form
		$INDEX_OF = & $rs['index_of']; // the address of an array of NN elements to convert Galois field elements in polynomial form to index (log) form
		$GENPOLY  = & $rs['genpoly']; // an array of NROOTS+1 elements containing the generator polynomial in index form
		$NROOTS   = & $rs['nroots']; // the number of roots in the RS code generator polynomial, which is the same as the number of parity symbols in a block
		$FCR      = & $rs['fcr']; // first consecutive root, index form
		$PRIM     = & $rs['prim']; // primitive element, index form
		$IPRIM    = & $rs['iprim']; // prim-th root of 1, index form
		$PAD      = & $rs['pad']; // the number of pad symbols in a block
		$A0       = & $NN;
		$parity   = \array_fill(0, $NROOTS, 0);
		for ($i = 0; $i < ($NN - $NROOTS - $PAD); ++$i) {
			$feedback = $INDEX_OF[$data[$i] ^ $parity[0]];
			if ($feedback != $A0) {
				// feedback term is non-zero
				// This line is unnecessary when GENPOLY[NROOTS] is unity, as it must
				// always be for the polynomials constructed by init_rs()
				$feedback = $this->modnn($rs, $NN - $GENPOLY[$NROOTS] + $feedback);
				for ($j = 1; $j < $NROOTS; ++$j) {
				$parity[$j] ^= $ALPHA_TO[$this->modnn($rs, $feedback + $GENPOLY[($NROOTS - $j)])];
				}
			}
			// Shift
			\array_shift($parity);
			if ($feedback != $A0) {
				\array_push($parity, $ALPHA_TO[$this->modnn($rs, $feedback + $GENPOLY[0])]);
			} else {
				\array_push($parity, 0);
			}
		}
		return $parity;
	}
} // end QRcode class

//============================================================+
// END OF FILE
//============================================================+
