SSIMPLEEXPRESSION' -> SIMPLEOPERATOR SCARDINALITY FIRST(SSIMPLEEXPRESSION')={e} u FIRST(SIMPLEOPERATOR)={e,plus,minus,multiply,divide}
| e

SSIMPLEEXPRESSION -> SCARDINALITY SSIMPLEEXPRESSION' FIRST(SSIMPLEEXPRESSION)=FIRST(SCARDINALITY)={verticalline}

ARGUMENT -> identifier FIRST(ARGUMENT)={identifier} u FIRST(CURLIEDSETEXP) u FIRST(WHOLENUMBER)={identifier,leftcurlybrace,minus,number}
| CURLIEDSETEXP
| WHOLENUMBER

ARGUMENTS' -> comma ARGUMENT|e FIRST(ARGUMENTS')={comma,e}

ARGUMENTS -> ARGUMENT ARGUMENTS' FIRST(ARGUMENTS)=FIRST(ARGUMENT)={identifier,leftcurlybrace,minus,number}

SFUNCTIONNAME -> venn FIRST(SFUNCTIONNAME)={venn,pointsetdiagram}
| pointsetdiagram

SFUNCTIONCALL -> SFUNCTIONNAME leftparenthesis ARGUMENTS rightparenthesis FIRST(SFUNCTIONCALL)=FIRST(SFUNCTIONNAME)={venn,pointsetdiagram}

SOFUNCTIONNAME -> add FIRST(SOFUNCTIONNAME)={add,delete}
| delete

SOFUNCTIONCALL -> identifier dot SOFUNCTIONNAME leftparenthesis ARGUMENTS rightparenthesis FIRST(SOFUNCTIONCALL)={identifier}

SCARDINALITY -> verticalline SETOPERATIONSIDE SEXPR' verticalline FIRST(SCARDINALITY)={verticalline}

WHOLENUMBER -> minus number FIRST(WHOLENUMBER)={minus,number}
| number.

SELEMENTOFNELEMENTOF' -> elementof SETOPERATIONSIDE FIRST(SELEMENTOFNELEMENTOF')={elementof,notelementof}
| notelementof SETOPERATIONSIDE

SELEMENTOFNELEMENTOF -> WHOLENUMBER SELEMENTOFNELEMENTOF' FIRST(SELEMENTOFNELEMENTOF)=FIRST(WHOLENUMBER)={minus,number}

POINT -> leftsquarebracket WHOLENUMBER comma WHOLENUMBER rightsquarebracket. FIRST(POINT)={leftsquarebracket}

POINTSETLITERAL' -> comma POINTSETLITERAL FIRST(POINTSETLITERAL')={comma,e}
| e.

POINTSETLITERAL -> POINT POINTSETLITERAL'. FIRST(POINTSETLITERAL)=FIRST(POINT)={leftsquarebracket}

SETLITERAL' -> comma SETLITERAL FIRST(SETLITERAL')={comma,e}
| e.
SETLITERAL -> WHOLENUMBER SETLITERAL'. FIRST(SETLITERAL)=FIRST(WHOLENUMBER)={minus,number}

SIMPLEOPERATOR -> plus FIRST(SIMPLEOPERATOR)={plus,minus,multiply,divide}
| minus 
| multiply 
| divide.

LOGICALRHS'' -> WHOLENUMBER FIRST(LOGICALRHS'')=FIRST(WHOLENUMBER) u {identifier}={minus,number,identifier}
| identifier.

LOGICALRHS' -> SIMPLEOPERATOR LOGICALRHS''|e. FIRST(LOGICALRHS')=FIRST(SIMPLEOPERATOR) u {e}={plus,minus,multiply,divide,e}

LOGICALRHS -> identifier LOGICALRHS'  FIRST(LOGICALRHS)={identifier} u FIRST(WHOLENUMBER)={minus,number,identifier}
| WHOLENUMBER LOGICALRHS'

DIVISIBILITYOPERATOR -> divides FIRST(DIVISIBILITYOPERATOR)={divides,doesnotdivide}
| doesnotdivide.

COMPARSIONOPERATOR -> greaterthanorequal  FIRST(COMPARSIONOPERATOR)={greaterthanorequal,lessthanorequal,greaterthan,lessthan,equal}
| lessthanorequal 
| greaterthan 
| lessthan 
| equal.

LOGICALSUBEXP -> leftparenthesis LOGICALEXP rightparenthesis FIRST(LOGICALSUBEXP)={leftparenthesis,identifier} u FIRST(WHOLENUMBER)={minus,number,leftparenthesis,identifier}
| identifier COMPARSIONOPERATOR LOGICALRHS 
| WHOLENUMBER DIVISIBILITYOPERATOR identifier.

LOGICALOPERATOR -> land FIRST(LOGICALOPERATOR)={land,lor}
| lor.

LOGICALEXP' -> LOGICALOPERATOR LOGICALEXP FIRST(LOGICALEXP')={e} u FIRST(LOGICALOPERATOR)={land,lor,e}
| e.

LOGICALEXP -> LOGICALSUBEXP LOGICALEXP'. FIRST(LOGICALEXP)=FIRST(LOGICALSUBEXP)={minus,number,leftparenthesis,identifier}

SETFORMULA -> identifier verticalline LOGICALEXP. FIRST(SETFORMULA)={identifier}

SETEXP -> SETLITERAL  FIRST(SETEXP)=FIRST(SETLITERAL) u FIRST(POINTSETLITERAL) u FIRST(SETFORMULA)={minus,number,leftsquarebracket,identifier}
| POINTSETLITERAL 
| SETFORMULA.

CURLIEDSETEXP -> leftcurlybrace SETEXP rightcurlybrace. FIRST(CURLIEDSETEXP)={leftcurlybrace}

SETOPERATIONSIDE -> identifier FIRST(SETOPERATIONSIDE)={identifier} u FIRST(CURLIEDSETEXP)={identifier,leftcurlybrace}
| CURLIEDSETEXP.

STESRUISC -> tobeequal CURLIEDSETEXP FIRST(STESRUISC)={tobeequal,equal,subsetof,realsubsetof,union,intersection,setminus,complement}
| equal SETOPERATIONSIDE
| subsetof SETOPERATIONSIDE
| realsubsetof SETOPERATIONSIDE
| union SETOPERATIONSIDE
| intersection SETOPERATIONSIDE
| setminus SETOPERATIONSIDE
| complement

SEXPR' -> STESRUISC | SOFUNCTIONCALL FIRST(SEXPR')={e} u FIRST(STESRUISC) u FIRST(SOFUNCTIONCALL)={e,tobeequal,equal,subsetof,realsubsetof,union,intersection,setminus,complement,identifier}
| e

SEXPR -> SETOPERATIONSIDE SEXPR' FIRST(SEXPR)=FIRST(SETOPERATIONSIDE) u FIRST(POINT)={identifier,leftcurlybrace,leftsquarebracket}
| POINT

STATEMENT  -> SEXPR FIRST(STATEMENT)=FIRST(SEXPR) u FIRST(SELEMENTOFNELEMENTOF) u FIRST(SFUNCTIONCALL) u FIRST(SSIMPLEEXPRESSION)={identifier,leftcurlybrace,leftsquarebracket,minus,number,venn,pointsetdiagram,verticalline}
|SELEMENTOFNELEMENTOF
|SFUNCTIONCALL
|SSIMPLEEXPRESSION

S  -> STATEMENT eol FIRST(S)=FIRST(STATEMENT)={identifier,leftcurlybrace,leftsquarebracket,minus,number,venn,pointsetdiagram,verticalline}