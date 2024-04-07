SSIMPLEEXPRESSION' -> SIMPLEOPERATOR SCARDINALITY SSIMPLEEXPRESSION' FIRST(SSIMPLEEXPRESSION')={e} u FIRST(SIMPLEOPERATOR)={e,plus,minus,multiply,divide}
|e

SSIMPLEEXPRESSION -> SCARDINALITY SSIMPLEEXPRESSION' FIRST(SSIMPLEEXPRESSION)=FIRST(SCARDINALITY)={verticalline}

ARGUMENT -> identifier FIRST(ARGUMENT)={identifier} u FIRST(CURLIEDSETEXP) u FIRST(WHOLENUMBER)={identifier,leftcurlybrace,minus,number}
|CURLIEDSETEXP
|WHOLENUMBER

ARGUMENTS' -> comma ARGUMENTS FIRST(ARGUMENTS')={comma,e}
|e

ARGUMENTS -> ARGUMENT ARGUMENTS' FIRST(ARGUMENTS)=FIRST(ARGUMENT)={identifier,leftcurlybrace,minus,number}

SFUNCTIONNAME -> venn FIRST(SFUNCTIONNAME)={venn,pointsetdiagram}
|pointsetdiagram

SFUNCTIONCALL -> SFUNCTIONNAME leftparenthesis ARGUMENTS rightparenthesis FIRST(SFUNCTIONCALL)=FIRST(SFUNCTIONNAME)={venn,pointsetdiagram}

SOFUNCTIONNAME -> add FIRST(SOFUNCTIONNAME)={add,delete}
|delete

SOFUNCTIONCALL -> dot SOFUNCTIONNAME leftparenthesis ARGUMENTS rightparenthesis FIRST(SOFUNCTIONCALL)={dot}

SCARDINALITY -> verticalline SCARDINALITY'  FIRST(SCARDINALITY)={verticalline}

SCARDINALITY'-> SETOPERATIONSIDE SEXPR' verticalline FIRST(SCARDINALITY')=FIRST(SETOPERATIONSIDE) u {leftparenthesis}={identifier,leftcurlybrace,leftparenthesis}
|leftparenthesis SETOPERATIONSIDE SEXPR' rightparenthesis UISC verticalline

WHOLENUMBER -> minus number FIRST(WHOLENUMBER)={minus,number}
|number.

SELEMENTOFNELEMENTOF' -> elementof SETOPERATIONSIDE FIRST(SELEMENTOFNELEMENTOF')={elementof,notelementof}
|notelementof SETOPERATIONSIDE

SELEMENTOFNELEMENTOF -> WHOLENUMBER SELEMENTOFNELEMENTOF' FIRST(SELEMENTOFNELEMENTOF)=FIRST(WHOLENUMBER)={minus,number}

POINT -> leftsquarebracket WHOLENUMBER comma WHOLENUMBER rightsquarebracket. FIRST(POINT)={leftsquarebracket}

POINTSETLITERAL' -> comma POINTSETLITERAL FIRST(POINTSETLITERAL')={comma,e}
|e.

POINTSETLITERAL -> POINT POINTSETLITERAL'. FIRST(POINTSETLITERAL)=FIRST(POINT)={leftsquarebracket}

SETLITERAL' -> comma SETLITERAL FIRST(SETLITERAL')={comma,e}
|e.

SETLITERAL -> WHOLENUMBER SETLITERAL'. FIRST(SETLITERAL)=FIRST(WHOLENUMBER)={minus,number}

SIMPLEOPERATOR -> plus FIRST(SIMPLEOPERATOR)={plus,minus,multiply,divide}
|minus 
|multiply 
|divide.

LOGICALRHS'' -> WHOLENUMBER FIRST(LOGICALRHS'')=FIRST(WHOLENUMBER) u {identifier}={minus,number,identifier}
|identifier.

LOGICALRHS' -> SIMPLEOPERATOR LOGICALRHS''  FIRST(LOGICALRHS')=FIRST(SIMPLEOPERATOR) u {e}={plus,minus,multiply,divide,e}
|e.

LOGICALRHS -> identifier LOGICALRHS'  FIRST(LOGICALRHS)={identifier} u FIRST(WHOLENUMBER)={minus,number,identifier}
|WHOLENUMBER LOGICALRHS'

DIVISIBILITYOPERATOR -> divides FIRST(DIVISIBILITYOPERATOR)={divides,doesnotdivide}
|doesnotdivide.

COMPARSIONOPERATOR -> greaterthanorequal  FIRST(COMPARSIONOPERATOR)={greaterthanorequal,lessthanorequal,greaterthan,lessthan,equal}
|lessthanorequal 
|greaterthan 
|lessthan 
|equal.

FUNCTIONDEFINITION'->identifier SIMPLEOPERATOR WHOLENUMBER FIRST(FUNCTIONDEFINITION')={identifier} u FIRST(WHOLENUMBER)={identifier,minus,number}
|WHOLENUMBER SIMPLEOPERATOR identifier.

FUNCTIONDEFINITION->arrow FUNCTIONDEFINITION'. FIRST(FUNCTIONDEFINITION)={arrow}

LOGICALSUBEXP' -> COMPARSIONOPERATOR LOGICALRHS FIRST(LOGICALSUBEXP')=FIRST(COMPARSIONOPERATOR) u FIRST(FUNCTIONDEFINITION)={greaterthanorequal,lessthanorequal,greaterthan,lessthan,equal,arrow}
|FUNCTIONDEFINITION.

LOGICALSUBEXP -> leftparenthesis LOGICALEXP rightparenthesis FIRST(LOGICALSUBEXP)={leftparenthesis,identifier} u FIRST(WHOLENUMBER)={minus,number,leftparenthesis,identifier}
|identifier LOGICALSUBEXP'
|WHOLENUMBER DIVISIBILITYOPERATOR identifier.

LOGICALOPERATOR -> land FIRST(LOGICALOPERATOR)={land,lor}
|lor.

LOGICALEXP' -> LOGICALOPERATOR LOGICALEXP FIRST(LOGICALEXP')={e} u FIRST(LOGICALOPERATOR)={land,lor,e}
|e.

LOGICALEXP -> LOGICALSUBEXP LOGICALEXP'. FIRST(LOGICALEXP)=FIRST(LOGICALSUBEXP)={minus,number,leftparenthesis,identifier}

SETFORMULA ->verticalline LOGICALEXP. FIRST(SETFORMULA)={verticalline}

IDENTIFIERLITERAL->comma identifier IDENTIFIERLITERAL FIRST(IDENTIFIERLITERAL)={comma,e}
|e.

SETEXP'->IDENTIFIERLITERAL FIRST(SETEXP')=FIRST(IDENTIFIERLITERAL) u FIRST(SETFORMULA)={comma,verticalline,e}
|SETFORMULA

SETEXP -> SETLITERAL  FIRST(SETEXP)=FIRST(SETLITERAL) u FIRST(POINTSETLITERAL) u {e, identifier}={minus,number,leftsquarebracket,identifier,e}
|POINTSETLITERAL 
|identifier SETEXP'
|e.

CURLIEDSETEXP -> leftcurlybrace SETEXP rightcurlybrace. FIRST(CURLIEDSETEXP)={leftcurlybrace}

SETOPERATIONSIDE -> identifier FIRST(SETOPERATIONSIDE)={identifier} u FIRST(CURLIEDSETEXP)={identifier,leftcurlybrace}
|CURLIEDSETEXP.


UISC->union STESRUISC' FIRST(UISC)={union,intersection,setminus,complement,e}
|intersection STESRUISC'
|setminus STESRUISC'
|complement UISC
|e.

STESRUISC'' -> UISC FIRST(STESRUISC'')=FIRST(UISC)={union,intersection,setminus,complement,e}

STESRUISC' -> SETOPERATIONSIDE STESRUISC'' FIRST(STESRUISC')={leftparenthesis,e} u FIRST(SETOPERATIONSIDE)={leftparenthesis,identifier,leftcurlybrace,e}
|leftparenthesis SETOPERATIONSIDE STESRUISC'' rightparenthesis STESRUISC''
|e.

TRHS->SETOPERATIONSIDE STESRUISC'' FIRST(TRHS)=FIRST(SETOPERATIONSIDE) u FIRST(POINT) u {leftparenthesis}={identifier,leftcurlybrace,leftsquarebracket,leftparenthesis}
|leftparenthesis SETOPERATIONSIDE STESRUISC'' rightparenthesis STESRUISC''
|POINT.

STESRUISC -> tobeequal TRHS FIRST(STESRUISC)={tobeequal,equal,subsetof,realsubsetof} u FIRST(UISC)={tobeequal,equal,subsetof,realsubsetof,e,union,intersection,setminus,complement}
|equal STESRUISC'
|subsetof STESRUISC'
|realsubsetof STESRUISC'
|UISC.

SEXPR' -> STESRUISC FIRST(SEXPR')=FIRST(STESRUISC) u FIRST(SOFUNCTIONCALL)={e,tobeequal,equal,subsetof,realsubsetof,union,intersection,setminus,complement,dot}
|SOFUNCTIONCALL.

SEXPR -> SETOPERATIONSIDE SEXPR' FIRST(SEXPR)={leftparenthesis} u FIRST(SETOPERATIONSIDE) u FIRST(POINT)={identifier,leftcurlybrace,leftsquarebracket,leftparenthesis}
|leftparenthesis SETOPERATIONSIDE STESRUISC'' rightparenthesis STESRUISC''
|POINT.

STATEMENT  -> SEXPR FIRST(STATEMENT)=FIRST(SEXPR) u FIRST(SELEMENTOFNELEMENTOF) u FIRST(SFUNCTIONCALL) u FIRST(SSIMPLEEXPRESSION)={identifier,leftcurlybrace,leftsquarebracket,leftparenthesis,minus,number,venn,pointsetdiagram,verticalline}
|SELEMENTOFNELEMENTOF
|SFUNCTIONCALL
|SSIMPLEEXPRESSION.

S  -> STATEMENT eol FIRST(S)=FIRST(STATEMENT)={identifier,leftcurlybrace,leftsquarebracket,leftparenthesis,minus,number,venn,pointsetdiagram,verticalline}