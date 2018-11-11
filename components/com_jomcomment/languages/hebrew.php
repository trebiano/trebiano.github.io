<?php 
/**
*
* @version $Id: hebrew.php,v 1.0.0 2006/09/09 14:47 MtK Exp $
* @package JomComment
* @subpackage languages
* @copyright Copyright (C) 2006-2007 Mati Kochen. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* 
* JomCooment - http://www.azrul.com
* Mati Kochen - mtk@012.net.il
*
*/

DEFINE("_JC_","אורח"); 
DEFINE("_JC_GUEST_NAME",        "אורח");

// Templates
DEFINE("_JC_TPL_ADDCOMMENT",    "הוסף תגובה");
DEFINE("_JC_TPL_AUTHOR",        "שם");
DEFINE("_JC_TPL_EMAIL",         "דוא\"ל");
DEFINE("_JC_TPL_WEBSITE",       "אתר");
DEFINE("_JC_TPL_COMMENT",       "תגובה");

DEFINE("_JC_TPL_TITLE",       	"כותרת");
DEFINE("_JC_TPL_WRITTEN_BY",    "נכתב ע\"י");


// Warning
DEFINE("_JC_CAPTCHA_MISMATCH",  "סיסמא שגויה");
DEFINE("_JC_INVALID_EMAIL",     "כתובת דוא\"ל שגויה");
DEFINE("_JC_USERNAME_TAKEN",    "שם זה רשום כבר. אנא נסה שם אחר");
DEFINE("_JC_NO_GUEST",          "שגיאת גישה. אנא הרשם");
DEFINE("_JC_IP_BLOCKED",        "כתובת ה-IP שלך חסומה");
DEFINE("_JC_DOMAIN_BLOCKED",    "כתובת הדומיין שלך חסומה");
DEFINE("_JC_MESSAGE_NEED_MOD",  "תגובתך נוספה. היא תאושר ע\"י מנהל המערכת");
DEFINE("_JC_MESSAGE_ADDED",     "תגובתך נוספה");

// New in 1.3
DEFINE("_JC_TPL_READMORE",       "קרא עוד");
DEFINE("_JC_TPL_COMMENTS",       "תגובות");   // plural
DEFINE("_JC_TPL_SEC_CODE",       "העתק תווים מוצגים");   // plural
DEFINE("_JC_TPL_SUBMIT_COMMENTS",       "הוסף תגובה");   // plural

// New in 1.4
DEFINE("_JC_EMPTY_USERNAME", "אנא הזן את שמך");
DEFINE("_JC_USERNAME_BLOCKED", "שמך נחסף");
DEFINE("_JC_TPL_WRITE_COMMENT",     "כתוב תגובה");
DEFINE("_JC_TPL_GUEST_MUST_LOGIN",  "הנך חייב להיות מחובר ע\"מ להזין תגובות. אנא הרשם, אם טרם עשית זאת.");
DEFINE("_JC_TPL_REPORT_POSTING",   "דווח למנהל");

DEFINE("_JC_TPL_NO_COMMENT",   "אין תגובות למאמר זה...");

// New in 1.5
DEFINE("_JC_TPL_HIDESHOW_FORM",   "הצג/הסתר טופס תגובה");
DEFINE("_JC_TPL_HIDESHOW_AREA",   "הצג/הסתר תגובות");
DEFINE("_JC_TPL_REMEMBER_INFO",   "זכור מידע?");

// New in 1.6
DEFINE("_JC_TPL_TOO_SHORT",   "תגובתך קצרה מידי");
DEFINE("_JC_TPL_TOO_LONG",   "תגובתך ארוכה מידי");
DEFINE("_JC_TPL_SUBSCRIBE",   "קבל דוא\"ל כאשר תפורסם תגובה");
DEFINE("_JC_TPL_PAGINATE_NEXT",   "תגובה הבאה");
DEFINE("_JC_TPL_PAGINATE_PREV",   "תגובה קודמת");

// New 1.6.8
DEFINE("_JC_TPL_DUPLICATE",   "Duplicate entry detected");
DEFINE("_JC_TPL_NOSCRIPT",   "Please enable JavaScript to post a new comment");

// New 1.7
DEFINE("_JC_TPL_INPUT_LOCKED", "This content has been locked. You can no longer post any comment.");
DEFINE("_JC_TPL_TRACKBACK_URI", "TrackBack URI for this entry");
DEFINE("_JC_TPL_COMMENT_RSS_URI", "RSS feed Comments");

?>
